<?php

//update phone state online/offline

require_once 'yarsap_14881.php';

$visitoraddress = getClientIP();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isSus($visitoraddress,'PhoneOnline') || isBlocked($visitoraddress)) {
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
            $newIsOnlineValue = $data->newstate;
           
            
            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {
                //die("Authentication failed $message");
                echo Format("Authentication failed $message",OP_Fail);
                BadLogin($visitoraddress,'PhoneOnline');
                die();
            }

        

            $conn = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
            // Set the PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare the statement to check if the token is valid and not expired
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");

            // Bind the parameters
             $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);

            // Execute the statement
            $stmt->execute();

            // Fetch the result
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if any rows were found
            if ($result) {


                // Construct the SQL query to update the isonline column
                $updateQuery = "UPDATE phones SET isonline = :isonlineValue WHERE phone_id = :pid";

                // Prepare and execute the query
                $stmt = $conn->prepare($updateQuery);
                $stmt->bindParam(':isonlineValue', $newIsOnlineValue, PDO::PARAM_INT);
                $stmt->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                $stmt->execute();

                // Check the number of affected rows
                $affectedRows = $stmt->rowCount();

                if ($affectedRows > 0) {
                 
                    echo Format("Online status updated successfully",OP_Success);
                } 



            } else {
                // Token is invalid or expired
                
                echo Format("Invalid or expired token.",OP_Fail);
                BadLogin($visitoraddress,'PhoneOnline');
            }
        } catch (PDOException $e) {
            echo Format("Error 2321: " . "Something went wrong , please try again later",OP_Fail);
            logError($e);
            BadLogin($visitoraddress,'PhoneOnline');
        }
        $conn = null;
    } else {
       
        echo Format( "Missing email or token data.",OP_Fail);
        BadLogin($visitoraddress,'PhoneOnline');
    }
} else {
   
    echo Format("Invalid request.",OP_Fail);
    BadLogin($visitoraddress,'PhoneOnline');
}
?>