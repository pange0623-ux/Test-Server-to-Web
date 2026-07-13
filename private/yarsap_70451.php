<?php

//this called from panel every 10 sec , to load user phones and check what online or not

date_default_timezone_set('Europe/London');


require_once 'yarsap_14881.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$visitoraddress = getClientIP();
if (isSus($visitoraddress,'LoadP_attempts') || isBlocked($visitoraddress)) {
   
    echo Format("To Many Request...",OP_Blocked);
    die();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  
    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->email) && !empty($data->token)) {


        try {
            $user_email = $data->email ?? 'empty';
            $user_token = $data->token ?? 'empty';
            $user_idf = $data->idf ?? 'empty';

            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {
            
                echo Format("Authentication failed $message",OP_Fail);
                BadLogin($visitoraddress,'LoadP_attempts');
                exit;
            }

            $conn =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $stmt = $conn->prepare("SELECT userid FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");


             $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);


            $stmt->execute();


            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($result) {

                $conn2 =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $userId = $result['userid'];
                $checkPhoneQuery = "SELECT * FROM phones WHERE user_id = :userId";
                $stmt2 = $conn2->prepare($checkPhoneQuery);
                $stmt2->bindParam(':userId', $userId, PDO::PARAM_INT);
                $stmt2->execute();
                $result2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

                if ($result2) {
                    $geoIpLibrary = new GeoIpLibrary('../assets/GeoIP/GeoLite2-City.mmdb');

                    //logdebug("Before filtering: ",json_encode($result2));

                    $result2 = array_filter($result2, function($row) {
                        return (string) $row['isRemoved'] !== "1";
                    });

                    $result2 = array_values($result2);

                   // logdebug("After filtering: ",json_encode($result2));

                    foreach ($result2 as &$row) {


                        $row['phone_id'] = (string) $row['phone_id'];
                        $currectphoneidf = (string) $row['session_id'];

                        $phonepingtime = new DateTime($row['last_ping']);
                        $currentDateTime = new DateTime('now');


                        if ($phonepingtime > $currentDateTime && $user_idf !== "empty" && $currectphoneidf !== "empty" && (string) $user_idf === $currectphoneidf) {
                            $row['last_ping'] = 1;
                        } else {
                            $row['last_ping'] = 0;
                        }
                  
                        $GeoIPinfo = $geoIpLibrary->getlocations($row['address']);
                        $row['longitude'] = $GeoIPinfo['longitude'];
                        $row['latitude'] = $GeoIPinfo['latitude'];
                    }

                    echo Format($result2,OP_Success);

               
                } else {
                  
                    echo Format("null",OP_Success);
                }

            } else {


                echo Format("Invalid or expired token.",OP_Fail);
                BadLogin($visitoraddress,'LoadP_attempts');
            }
        } catch (PDOException $e) {
         
            echo Format("Error: " . "Something went wrong , please try again later",OP_Fail);
            logError($e);
        }
        $conn = null;
    } else {
        
        echo Format("Missing email or token data.",OP_Fail);
        BadLogin($visitoraddress,'LoadP_attempts');
    }
} else {
    echo Format("Invalid request.",OP_Fail);
    BadLogin($visitoraddress,'LoadP_attempts');
}
?>