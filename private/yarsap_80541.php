<?php

date_default_timezone_set('Europe/London');

require_once 'yarsap_14881.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	sleep(1);
  //  session_start();
    
    // Rate-limiting to prevent overwhelming the server
    // $lastCall = $_SESSION['last_call_time'] ?? 0;
    // $currentCall = time();
    // if ($currentCall - $lastCall < 5) {
        // echo "CO:" . "Sleep";
        // exit();
    // }
    // $_SESSION['last_call_time'] = $currentCall;

    try {
        // Use persistent database connection
        $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (!isset($_POST['user_email'])) {
            die('Invalid request');
        }

        $userEmail = $_POST['user_email'] ?? 'empty';

        // Check for valid user
        $getUserIDQuery = "SELECT userid FROM users WHERE email = ?";
        $stmt = $pdo->prepare($getUserIDQuery);
        $stmt->execute([$userEmail]);
        $userId = $stmt->fetchColumn();

        if ($userId) {
            $stmt_idf = $pdo->prepare("SELECT idf FROM nodeidfs WHERE user_id = :uuid AND timestamp >= NOW() AND ismain = '1'");
            $stmt_idf->bindParam(':uuid', $userId);
            $stmt_idf->execute();
            $existing_id = $stmt_idf->fetchColumn();

            if (!$existing_id) {
                echo "CO:" . "Sleep";
                exit();
            }

            $phone_idf = $existing_id;
            $authkeys = [
                "idf" => $phone_idf,
                "sk" => 'ws://[server_ip]:8080<',//server ip
                "cip" => getClientIP(),
                "ad" => '[server_ip]' // Current server IP address , example 10.10.10.10
            ];
            $phoneauth = json_encode($authkeys);
            echo "Conf:" . $phoneauth;
        } else {
            echo "Invalid request.";
        }
    } catch (Exception $th) {
        logError($th);
        echo "CO:" . "Sleep";
    } finally {
        $pdo = null; // Close connection
    }
} else {
    echo "Invalid request method.";
}
?>
