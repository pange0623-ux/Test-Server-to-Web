<?php

//this called from panel settings page to change user password


require_once __DIR__.'/../vendor/autoload.php';


require_once 'yarsap_14881.php';


use RobThree\Auth\TwoFactorAuth;




if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$visitoraddress = getClientIP();
if (isSus($visitoraddress, 'ChangePass') || isBlocked($visitoraddress)) {
    die('Blocked');
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
function validateAndGetUserId($email, $token, $oldpass)
{

    try {

        $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $stmt = $pdo->prepare('SELECT userid,password FROM users WHERE email = :email AND token = :token');
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) { 
            return null;
        }


        $stord_pass = $result['password'];
        

        if (!password_verify($oldpass, $stord_pass)) {
            return null;
        }

        

        $userId = $result['userid'];
        $pdo = null;


        return ($userId !== false) ? $userId : null;
    } catch (PDOException $e) {

        logError($e);
        echo Format('031 Something went wrong please try again later.', OP_Fail);
        return null;
    }
}


if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['opass']) &&
    isset($_POST['npass']) &&
    isset($_POST['npassconf']) &&
    isset($_POST['token']) &&
    isset($_POST['email'])
) {


    $email = $_POST['email'];

    $token = $_POST['token'];
    $oldpass = $_POST['opass'];
    $newpass = $_POST['npass'];
    $newpassconf = $_POST['npassconf'];



    list($isValid, $message) = SessionCheck($_POST['email'], $token);

    if (!$isValid) {
        echo "Authentication failed $message";
        BadLogin($visitoraddress, 'ChangePass');
        exit;
    }

    $userId = validateAndGetUserId($email, $token, $oldpass);
    if ($userId === null) {
        echo "Unauthorized Access!!!" ;
        BadLogin($visitoraddress, 'ChangePass');
        exit;
    }

    $usersalt = getusersalt($email, $token);

    if ($usersalt === null || strlen($usersalt) === 0) {
        echo "To change your password \n Please Enable (2FA) first.";
        BadLogin($visitoraddress, 'ChangePass');
        exit();
    }

    if ($newpass !== $newpassconf) {
        echo "new password confirm not matching.";
        BadLogin($visitoraddress, 'ChangePass');
        exit();
    }


    if (!isset($_POST['scode'])) {
        echo "Unauthorized Access (2FA)!!!";
        BadLogin($visitoraddress, 'ChangePass');
        exit;
    }



    try {
        $facode = $_POST['scode'];

        $tfa = new TwoFactorAuth();

        // Verify the code
        $result = $tfa->verifyCode(DE($usersalt), $facode);
        if (empty($result)) {
            echo 'Please Check your 2FA Code.';
            BadLogin($visitoraddress, 'ChangePass');
            exit;
        } else {
            updatepass($userId, $newpass);
            echo 'YES';
        }
    } catch (\Throwable $th) {
        echo 'Something went wrong please try again later : x1.';
        logError($th);
        exit;
    }
}
function updatepass($userId, $newpass)
{
   

    try {

        $hash = password_hash($newpass, PASSWORD_DEFAULT);


        // Replace 'your_database_host', 'your_database_name', 'your_database_user', and 'your_database_password'
        $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare a statement to update the 'profilepic' column
        $stmt = $pdo->prepare('UPDATE users SET password = :pas WHERE userid = :userId');
        $stmt->bindParam(':pas', $hash);
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();

        // Close the database connection
        $pdo = null;
    } catch (PDOException $e) {
        // Handle database connection errors
        logError($e);
        echo Format('964 Something went wrong please try again later.', OP_Fail);
    }
}
