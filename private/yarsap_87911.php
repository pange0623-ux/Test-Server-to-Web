<?php

//manage 2FA (otp) , enable , reset

require_once __DIR__.'/../vendor/autoload.php';

require_once 'yarsap_14881.php';

use RobThree\Auth\TwoFactorAuth;



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$visitoraddress = getClientIP();
if (isSus($visitoraddress, '2famanage') || isBlocked($visitoraddress)) {

    echo Format("To Many Request...", OP_Blocked);
    die();
}


if (!FloodCheck('2famanage')) {
    echo Format("To Many Request \n Please Slow Down", OP_Fail);
    die();
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code'])) {

    $code = $_POST['code'] ?? 'empty';

    $email = $_POST['email'] ?? 'empty';
    $token = $_POST['token'] ?? 'empty';

    // list($isValid, $message) = SessionCheck($email, $token);

    // if (!$isValid) {
    //     echo Format("Authentication failed $message", OP_Fail);
    //     BadLogin($visitoraddress, '2famanage');
    //     die();
    // }

    $userId = validateAndGetUserId($email);
    if ($userId == null) {
        echo "Unauthorized Access!!!";
        BadLogin($visitoraddress, '2famanage');
        die();
    }

    //---------------

    if (!isset($_SESSION['2FA_Skey'])) {
        echo Format('Invalid login (1), try reload the page', OP_Fail);
        BadLogin($visitoraddress, '2famanage');
        die();
    }

    //-----------------

    if (!isset($_SESSION['sesioncheck'])) {
        echo Format('Invalid login (2), try reload the page', OP_Fail);
        BadLogin($visitoraddress, '2famanage');
        die();
    }

    $sesioncheck = $_SESSION['sesioncheck'];
    $currentcheck = md5($visitoraddress . ":" . $_SERVER['HTTP_USER_AGENT'] . ":" . $token);

    $secret_key = $_SESSION['2FA_Skey'];

    if ($sesioncheck !== $currentcheck) {

        echo Format('Invalid login (3), try reload the page', OP_Fail);
        BadLogin($visitoraddress, '2famanage');
        die();
    }


    $tfa = new TwoFactorAuth();


    $result = $tfa->verifyCode(DE($secret_key), $code);


    if (empty($result)) {

        echo Format('Please check your 2FA Code', OP_Fail);
        BadLogin($visitoraddress, '2famanage');
        die();
    } else {
        updatesalt($userId, $secret_key);
        echo Format('OK', OP_Success);
        exit();
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'PUT') {



    $inputData = file_get_contents("php://input");

    $data = json_decode($inputData, true);


    $email = $data['email'] ?? die();
    $token = $data['token'] ?? die();
    $code = $data['code'] ?? die();


    if (!isset($_SESSION['2FA_Skey'])) {
        echo Format('Invalid login (1), try reload the page', OP_Fail);
        BadLogin($visitoraddress, '2famanage');
        die();
    }
    
    //-----------------

    if (!isset($_SESSION['sesioncheck'])) {
        echo Format('Invalid login (2), try reload the page', OP_Fail);
        BadLogin($visitoraddress, '2famanage');
        die();
    }

    $sesioncheck = $_SESSION['sesioncheck'];
    $currentcheck = md5($visitoraddress . ":" . $_SERVER['HTTP_USER_AGENT'] . ":" . $token);

    

    if ($sesioncheck !== $currentcheck) {

        echo Format('Invalid login (3), try reload the page', OP_Fail);
        BadLogin($visitoraddress, '2famanage');
        die();
    }



    list($isValid, $message) = SessionCheck($email, $token);

    if (!$isValid) {
        echo Format("Authentication failed $message", OP_Fail);
        BadLogin($visitoraddress, '2famanage');
        die();
    }

    $userId = validateAndGetUserId($email, $token);
    if ($userId === null) {
        echo Format('Unauthorized Access!!!', OP_Fail);
        BadLogin($visitoraddress, '2famanage');
        exit;
    }

    $usersalt = getusersalt($email, $token);


    if ($usersalt !== null && strlen($usersalt) > 0) {
        $tfa = new TwoFactorAuth();


        $result = $tfa->verifyCode(DE($usersalt), $code);
        if (empty($result)) {

            echo Format('Please check your 2FA Code.', OP_Fail);
            BadLogin($visitoraddress, '2famanage');
        } else {
            updatesalt($userId, null);
            echo Format('Done', OP_Success);
        }
    } else {

        echo Format('2FA is not Enabled !!!', OP_Fail);
    }
} else {

    echo Format('Enter a code and click "Verify"', OP_Fail);
    BadLogin($visitoraddress, '2famanage');
}



function updatesalt($userId, $salt)
{

    try {

        $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);


        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $stmt = $pdo->prepare('UPDATE users SET otp_salt = :slt WHERE userid = :userId');
        $stmt->bindParam(':slt', $salt);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();


        $pdo = null;
    } catch (PDOException $e) {
        logError($e);
        echo Format('613 Something went wrong please try again later.', OP_Fail);
    }
}
function validateAndGetUserId($email)
{


    try {
        
        $pdo =new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $stmt = $pdo->prepare('SELECT userid FROM users WHERE email = :email');
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $userId = $stmt->fetchColumn();


        $pdo = null;

   
        return ($userId !== false) ? $userId : null;
    } catch (PDOException $e) {

        logError($e);
     
        return null;
    }
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
        echo Format('964 Something went wrong please try again later.', OP_Fail);
        return null;
    }
}
