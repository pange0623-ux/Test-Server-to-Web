<?php

//this for logout , when user click logout in panel

require_once 'yarsap_14881.php';



$visitoraddress = getClientIP();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isSus($visitoraddress,'Logout') || isBlocked($visitoraddress)) {
    echo Format('Blocked',OP_Blocked);
    die();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if ( $data && !empty($data->email) && !empty($data->token)) {

        try {
            $user_email = $data->email ?? 'empty';
            $user_token = $data->token ?? 'empty';
            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {
            
                echo Format("Authentication failed $message",OP_Fail);
                BadLogin($visitoraddress,'Logout');
                exit;
            }

            $conn =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare the statement to set 'token' and 'token_expiration' to NULL
            $stmt = $conn->prepare("UPDATE users SET token = NULL, token_expiration = NULL WHERE email = :email AND token = :tok");

            // Bind the parameter
            $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':tok', $user_token);

            // Execute the statement
            $stmt->execute();

            // Remove the 'user_email' and 'user_token' cookies
            setcookie('user_email', '', time() - 3600, '/');
            setcookie('user_token', '', time() - 3600, '/');



            session_unset();

            session_destroy();


            echo Format("ok",OP_Success);
        } catch (PDOException $e) {
          
            echo Format("Error: " . "Something went wrong , please try again later",OP_Fail);
            logError($e);
        }
        $conn = null;
    } else {
   
        echo Format("Error: Missing data.",OP_Fail);
        BadLogin($visitoraddress,'Logout');
    }
} else {
    
    echo Format("Invalid request.",OP_Fail);
    BadLogin($visitoraddress,'Logout');
}
?>