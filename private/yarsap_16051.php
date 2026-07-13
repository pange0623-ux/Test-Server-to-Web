<?php

//extra check if user login is valid

require_once 'yarsap_14881.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->email) && !empty($data->token)) {


        try {
            $user_email = $data->email ?? 'empty';
            $user_token = $data->token ?? 'empty';
			
           if(session_status() === PHP_SESSION_NONE){
            session_start();
           }
            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {
                echo Format("Authentication failed $message", OP_Fail);
                die();
            }

            $conn = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
           
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

           
            $stmt = $conn->prepare("SELECT Expire FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");

         
             $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);

            $stmt->execute();

     
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

        
            if ($result) {
                
                $expireDate = new DateTime($result['Expire']);
                $currentDate = new DateTime();
                if ($currentDate > $expireDate) {
               
                   
                    echo Format('expired',OP_Fail);
                } else {
                    if(!isset($_SESSION['pid'])){
                        echo Format("Invalid login.", OP_Fail);
                        die();
                    }
                    $pid = $_SESSION['pid'];
                    echo Format("$pid",OP_Success);
                }
                // Valid token and not expired
                
            } else {
               
                echo Format("Invalid or expired token.",OP_Fail);
            }
        } catch(PDOException $e) {
            logError($e);
            echo Format("something went wrong 444.",OP_Fail);
        }
        $conn = null;
    } else {
      
        echo Format("Missing email or token data.",OP_Fail);
    }
} else {
    echo Format("Invalid request.",OP_Fail);
}



?>
