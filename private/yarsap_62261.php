<?php

//this to load payments from DB
//i leave it for you , you can add payments manully in DB

require_once 'yarsap_14881.php';

$visitoraddress = getClientIP();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isSus($visitoraddress, 'fetch_pays') || isBlocked($visitoraddress)) {
    echo Format('Blocked', OP_Blocked);
    die();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->email) && !empty($data->token) && !empty($data->pid)) {

        try {
            $user_email = $data->email ?? 'empty';
            $user_token = $data->token ?? 'empty';
            $pid = $data->pid ?? 'empty';



            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {

                echo Format("Authentication failed $message", OP_Fail);
                BadLogin($visitoraddress, 'fetch_pays');
                die();
            }


            $postcheck = md5($user_email . ":" . $user_token . ":" . $visitoraddress . ":" . $_SERVER['HTTP_USER_AGENT']);

            if ($pid !== $postcheck) {
                echo Format("Authentication failed (Session)", OP_Fail);
                BadLogin($visitoraddress, 'fetch_pays');
                die();
            }


            $conn =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $stmt = $conn->prepare("SELECT userid FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");


            $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);


            $stmt->execute();


            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($result) {

                $userid = $result['userid'];

                $conn2 =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $checkOnlineQuery = "SELECT invoice_id,crypto,payment_state,total_paid,payment_date,transaction_id FROM payments WHERE userid = :uuid";
                $stmt1 = $conn2->prepare($checkOnlineQuery);
                $stmt1->bindParam(':uuid', $userid, PDO::PARAM_INT);
                $stmt1->execute();

                if ($stmt1->rowCount() > 0) {
                    
                    $results = $stmt1->fetchAll(PDO::FETCH_ASSOC);
                    
                    echo Format( $results, OP_Success);
                } else {

                    echo Format("NOPAY.", OP_Fail);
                  
                }

                exit();
            } else {


                echo Format("Invalid or expired token.", OP_Fail);
                BadLogin($visitoraddress, 'fetch_pays');
            }
        } catch (PDOException $e) {
            echo Format("Error: " . "Something went wrong , please try again later", OP_Fail);
            logError($e);


            BadLogin($visitoraddress, 'fetch_pays');
        }
        $conn = null;
    } else {

        echo Format("Missing email or token data.", OP_Fail);
        BadLogin($visitoraddress, 'fetch_pays');
    }
} else {

    echo Format("Invalid request.", OP_Fail);
    BadLogin($visitoraddress, 'fetch_pays');
}
