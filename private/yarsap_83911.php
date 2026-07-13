<?php

//this for sending notifications to client

require_once 'yarsap_14881.php';


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
       // echo "Error decoding JSON: " . json_last_error_msg();
        echo Format("Decoding Error",OP_Fail);
        exit;
    }
    if (!empty($data->email) && !empty($data->token)) {
       
  
        try {
            $user_email = $data->email ?? 'empty';
            $user_token = $data->token ?? 'empty';
            $comand = $data->command;
            // session_start();
            // list($isValid, $message) = SessionCheck($user_email, $user_token);

            // if (!$isValid) {
            //     echo Format("Authentication failed ,$message",OP_Fail);
            //     exit;
            // }


            $conn =new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");

            $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token',$user_token);

            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            

            if ($result) {
              
                $expireDate = new DateTime($result['Expire']);
                $currentDate = new DateTime();
                if ($currentDate > $expireDate) {
                    
                    echo Format("Your subscription has expired.",OP_Fail);
                } else {

                    switch ($comand) {
                        case "new":
                            $random_id = bin2hex(random_bytes(8));
                            $userid = $result['userid'];
                            $alertstate = "1";
                            $alertmsg = $data->msg;
                            $alertico = $data->ico;
                            $alertitle = $data->title;
                            $alertauthor = $result['authorty'];
                           

                            $conn2 = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                            $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            //update here the query to add
                            $stmt2 = $conn2->prepare("INSERT INTO `alerts` (`notifi_id`, `user_id`, `state`, `alert_title` , `content`, `alert_ico`, `author`) VALUES (:alert_id, :userid, :alertstate , :title , :alertmsg, :alertico, :alertauthor)");
                            $stmt2->bindParam(':alert_id', $random_id);
                            $stmt2->bindParam(':userid', $userid);
                            $stmt2->bindParam(':alertstate', $alertstate);
                            $stmt2->bindParam(':alertmsg', $alertmsg);
                            $stmt2->bindParam(':alertico', $alertico);
                            $stmt2->bindParam(':alertauthor', $alertauthor);
                            $stmt2->bindParam(':title', $alertitle);


                            if ($stmt2->execute()) {
                                echo Format("ok",OP_Success);
                            } else {
                                echo Format("Error new Notify..",OP_Fail);
                            }
                            break;
                        case "load":
                            $userid = $result['userid'];
                            $conn2 = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                            $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $stmt2 = $conn2->prepare("SELECT `content`, `author`, `notifi_id`, `state`, `alert_ico` , `alert_title`  FROM alerts WHERE user_id = :userid");
                            $stmt2->bindParam(':userid', $userid);
                            $stmt2->execute();

                            $alerts = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                            
                            echo Format($alerts,OP_Success);
                            break;
                        case "seen":
                            $random_id = $data->alertid;

                            //here update alerts set state=0 using $alert_id
                            //and echo ok like below if row effected
                           // $alert_id = $data->alertid;

                            $conn2 =new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                            $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $stmt2 = $conn2->prepare("UPDATE alerts SET state = 0 WHERE notifi_id = :alert_id");
                            $stmt2->bindParam(':alert_id', $random_id);

                            if ($stmt2->execute()) {
                                 
                                echo Format("ok",OP_Success);
                            } else {
                                
                                
                                echo Format("Error seen Notify..",OP_Fail);
                            }

                            break;
                        case "remove":
                            $random_id = $data->alertid;

                            $conn2 = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                    
                            $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            $stmt2 = $conn2->prepare("DELETE FROM alerts WHERE notifi_id = :alert_id");
                            $stmt2->bindParam(':alert_id', $random_id);
                       
                    
                            if ($stmt2->execute()) {
                                 
                                echo Format("ok",OP_Success);
                            } else {
                              
                                
                                echo Format("Error Remove Notify..",OP_Fail);
                            }
                            break;
                    }


                }
            } else {

                echo Format("Invalid or expired token.",OP_Fail);
            }
        } catch (PDOException $e) {
         
            echo Format("Error: " . "Something went wrong , please try again later",OP_Fail);
            logError($e);
        }
        $conn = null;
    } else {
       
        echo Format("Missing email or token data.",OP_Fail);
    }
} else {
    echo Format("Invalid request.",OP_Fail);
}

?>