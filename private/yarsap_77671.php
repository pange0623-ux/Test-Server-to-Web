<?php

//fetch SMS from DB and send to panel.

require_once 'yarsap_14881.php';


session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->email) && !empty($data->token) && !empty($data->phoneid)) {

        try {
            $user_email = $data->email ?? 'empty';
            $user_token = $data->token ?? 'empty';
            $phone_id = $data->phoneid ?? 'empty';



           
            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {
                die("Authentication failed $message");
            }


            $conn =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
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

                $userid = $result['userid'];

                try {
                    // Establish a database connection using PDO
                    $pdo =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                    // Set PDO to throw exceptions for errors
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // Get the phone ID from the request (you may want to validate and sanitize the input)


                    $query = "SELECT sms_id,sms_address,sms_name,sms_date,sms_content,sms_tag,sms_type FROM sms WHERE phone_id = :phoneId";

                    $stmt = $pdo->prepare($query);

                    $stmt->bindParam(':phoneId', $phone_id, PDO::PARAM_STR);

                    $stmt->execute();

                    $result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $fileList = null;

                    if ($result2) {
                        echo "[>K<]" . json_encode($result2);


                        $queryremove = "DELETE From sms WHERE user_id = :uuid or phone_id = :phoneId";
                        $stmt = $pdo->prepare($queryremove);

                        $stmt->bindParam(':uuid', $userid, PDO::PARAM_STR);
                        $stmt->bindParam(':phoneId', $phone_id, PDO::PARAM_STR);

                        $stmt->execute();
                        return;

                    } else {

                        $fileList = null;
                    }

                    header('Content-Type: application/json');
                    echo json_encode($fileList);

                } catch (PDOException $e) {
                    logError($e);
die('Connection failed');
                }
            } else {
                // Token is invalid or expired
                echo "Invalid or expired token.";
            }
        } catch (PDOException $e) {
            logError($e);
echo Format('784 Something went wrong please try again later.', OP_Fail);
        }
        $conn = null;
    } else {
        echo "Invalid request param.";
    }
} else {
    echo "Invalid request.";
}
?>