<?php


//load phones apps from DB to panel


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

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $stmt = $conn->prepare("SELECT userid FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");


            $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);


            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($result) {

                $userid = $result['userid'];

                try {

                    $pdo =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);


                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);



                    $query = "SELECT app_name,app_ico,app_type,app_pkg,app_date,app_permissions,app_receivers,app_activitys FROM phone_apps WHERE phone_id = :phoneId";

                    $stmt = $pdo->prepare($query);

                    $stmt->bindParam(':phoneId', $phone_id, PDO::PARAM_STR);

                    $stmt->execute();

                    $result2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $fileList = null;

                    if ($result2) {
                        echo "[>K<]" . json_encode($result2);


                        $queryremove = "DELETE From phone_apps WHERE user_id = :uuid or phone_id = :phoneId";
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
            echo Format('630 Something went wrong please try again later.', OP_Fail);
        }
        $conn = null;
    } else {
        echo "Invalid request param.";
    }
} else {
    echo "Invalid request.";
}
