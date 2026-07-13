<?php

//this called from panel, to update phones APK options.
//like enable/disable notification monitor

require_once 'yarsap_14881.php';


session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->email) && !empty($data->token) && !empty($data->phoneid)) {


        try {
            $user_email = $data->email ?? 'empty';
            $user_token = $data->token ?? 'empty';
            $phone_id = $data->phoneid ?? 'empty';
            $option = $data->option ?? 'empty';
            $optionNewValue =  $data->optionvalue ?? 'empty';



            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {
                die("Authentication failed $message");
            }


            $whitelist = array(
                '1',
                '0'
            );
            if (!in_array($optionNewValue, $whitelist)) {
                die('Unkown Value');
            }

            $conn =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");


            $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);


            $stmt->execute();


            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($result) {

                $userid = $result['userid'];

                try {

                    $pdo =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);


                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);




                    $query = "SELECT phone_options FROM phones WHERE phone_id = :phoneId";

                    $stmt = $pdo->prepare($query);

                    $stmt->bindParam(':phoneId', $phone_id, PDO::PARAM_STR);

                    $stmt->execute();

                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($row) {
                        $jsonString = $row['phone_options'];
                        $jsonData = json_decode($jsonString, true);
                        if (array_key_exists($option, $jsonData)) {
                            $jsonData[$option] = $optionNewValue;
                            $newJsonString = json_encode($jsonData);

                            // Step 5: Update the Database
                            $updateQuery = "UPDATE phones SET phone_options = :optn WHERE phone_id = :phoneId";
                            $updateStmt = $conn->prepare($updateQuery);
                            $updateStmt->bindParam(':optn', $newJsonString, PDO::PARAM_STR);
                            $updateStmt->bindParam(':phoneId', $phone_id, PDO::PARAM_STR);
                            $updateStmt->execute();

                            echo "ok";
                        } else {
                            echo "no";
                        }
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

                echo "Invalid or expired token.";
            }
        } catch (PDOException $e) {
            logError($e);
            echo Format('048 Something went wrong please try again later.', OP_Fail);
        }
        $conn = null;
    } else {
        echo "Invalid request param.";
    }
} else {
    echo "Invalid request.";
}
