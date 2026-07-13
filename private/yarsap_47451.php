<?php

//this called from the panel one time at start,
//to check if the 2FA (otp) is enabled or not
//then display in settings page

require_once __DIR__.'/../vendor/autoload.php';

require_once 'yarsap_14881.php';

use RobThree\Auth\TwoFactorAuth;

$visitoraddress = getClientIP();
if (isSus($visitoraddress, '2facheck') || isBlocked($visitoraddress)) {

    echo Format("To Many Request...", OP_Blocked);
    die();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

   
    $email = $_POST['email'] ?? die();
    $token = $_POST['token'] ?? die();
    $pid = $_POST['pid'] ?? die();

    if (!isset($_SESSION)) {
        session_start();
    }

    list($isValid, $message) = SessionCheck($email, $token);

    if (!$isValid) {
        echo Format("Authentication failed $message", OP_Fail);
        BadLogin($visitoraddress, '2facheck');
        die();
    }


    $postcheck = md5($email . ":" . $token . ":" . $visitoraddress . ":" . $_SERVER['HTTP_USER_AGENT']);

    if ($pid !== $postcheck) {
        echo Format("Authentication login failed", OP_Fail);
        BadLogin($visitoraddress, '2facheck');
        die();
    }

    
    $usersalt = getusersalt($email, $token);

    if ($usersalt !== null && strlen($usersalt) > 0) {
        
        $sesioncheck = md5($visitoraddress . ":" . $_SERVER['HTTP_USER_AGENT'].":".$token);

        $_SESSION['sesioncheck'] = $sesioncheck;
        $_SESSION['2FA_Skey'] = $usersalt;

        echo Format('linked',OP_Success);
    } else {

        $tfa = new TwoFactorAuth();
        $secret = $tfa->createSecret();
        $domain = $_SERVER['SERVER_NAME'];
        $qrCodeImage = $tfa->getQRCodeImageAsDataUri($domain.":" . DE($email), $secret);

        $sesioncheck = md5($visitoraddress . ":" . $_SERVER['HTTP_USER_AGENT'].":".$token);

        $_SESSION['sesioncheck'] = $sesioncheck;
        $_SESSION['2FA_Skey'] = EN($secret);

        $data = [
            'QR' => $qrCodeImage
        ];
        echo Format($data,OP_Request);
    }
} else {
    echo Format('Invalid request.',OP_Fail);
}



function getusersalt($email, $token)
{


    try {
        
        $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare('SELECT otp_salt FROM users WHERE email = :email AND token = :token');
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $userId = $stmt->fetchColumn();

        $pdo = null;
    
        return ($userId !== false) ? $userId : null;
    } catch (PDOException $e) {
        logError($e);
        echo Format('345 Something went wrong please try again later',OP_Fail);
        return null;
    }
}


?>