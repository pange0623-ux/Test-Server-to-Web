<?php
require_once 'yarsap_14881.php';

// $whitelist = array(
    // '127.0.0.1',
    // '::1'
// );


$visitoraddress = getClientIP();

// if (!in_array($visitoraddress, $whitelist)) {
    // header("HTTP/1.0 404 Not Found");
    // echo file_get_contents('../404.php');
    // exit();
// }

function isUserIDTaken($userID)
{


    try {

        $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);


        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $stmt = $pdo->prepare('SELECT COUNT(*) AS count FROM users WHERE userid = :uuid');
        $stmt->bindParam(':uuid', $userID);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    } catch (PDOException $e) {

        logError($e);

        echo Format("ops , something wrong , try again later (21)", OP_Fail);
        die();
    }
}
function insertPayment($userId, $crypto, $paymentState, $paymentAmount, $additionalInfo, $invocid, $subtype_pay)
{
    try {

        $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $serializedAdditionalInfo = serialize($additionalInfo);

        $stmt = $pdo->prepare("INSERT INTO payments (paymentid, userid,invoice_id, crypto, payment_state, payment_amount, payment_date,subtype, additional_information, transaction_id) 
                                VALUES (NULL, :userId,:vid, :crypto, :paymentState, :paymentAmount, current_timestamp(),:subt, :additionalInfo, NULL)");


        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':vid', $invocid, PDO::PARAM_STR);
        $stmt->bindParam(':crypto', $crypto, PDO::PARAM_STR);
        $stmt->bindParam(':paymentState', $paymentState, PDO::PARAM_STR);
        $stmt->bindParam(':paymentAmount', $paymentAmount, PDO::PARAM_STR);
        $stmt->bindParam(':subt', $subtype_pay, PDO::PARAM_STR);
        $stmt->bindParam(':additionalInfo', $serializedAdditionalInfo, PDO::PARAM_STR);

        $stmt->execute();
        $pdo = null;

        return true;
    } catch (PDOException $e) {
        logError($e);
        return false;
    }
}
function updatepayment($pdo, $txid, $total_paid, $invoice_id)
{
    $stmt = $pdo->prepare("UPDATE payments SET transaction_id = :txid, payment_state = 'success', total_paid = :amount_fiat WHERE invoice_id = :external_id");
    $stmt->bindParam(':txid', $txid, PDO::PARAM_STR);
    $stmt->bindParam(':amount_fiat', $total_paid, PDO::PARAM_STR);
    $stmt->bindParam(':external_id', $invoice_id, PDO::PARAM_STR);
    $stmt->execute();
}
function isIDTaken($invocid)
{


    try {

        $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);


        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $stmt = $pdo->prepare('SELECT COUNT(*) AS count FROM payments WHERE invoice_id = :vid');
        $stmt->bindParam(':vid', $invocid);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    } catch (PDOException $e) {

        logError($e);
        echo Format('493 Something went wrong please try again later.', OP_Fail);
        die();
    }
}
function Creatuserfolder($UserID)
{
    $baseDirectory = '../user/storage/';
    $AppsDirectory = '../user/apps/';
    $assetsImagePath = '../assets/imgs/useracc.png'; // Path to the default image
    $userwalldir = $baseDirectory . $UserID . '/' . 'wall';

    // Create the wall directory
    if (!is_dir($userwalldir)) {
        mkdir($userwalldir, 0777, true);
        logdebug('Filesystem', 'User wall directory created: ' . $userwalldir);
    }

    // Copy image and rename to Prof.png
    $destinationImage = $userwalldir . '/Prof.png';
    if (file_exists($assetsImagePath)) {
        if (copy($assetsImagePath, $destinationImage)) {
            logdebug('Filesystem', 'Image copied to user wall directory and renamed to Prof.png for user: ' . $UserID);
        } else {
            logdebug('Filesystem', 'Failed to copy image for user: ' . $UserID);
        }
    } else {
        logdebug('Filesystem', 'Source image not found: ' . $assetsImagePath);
    }

    $userDirectory = $baseDirectory . $UserID . '/';
    $userAppsDirectory = $AppsDirectory . $UserID . '/';

    if (!is_dir($userDirectory)) {
        mkdir($userDirectory, 0777, true);
        logdebug('Filesystem', 'User directory created: ' . $userDirectory);
    }

    if (!is_dir($userAppsDirectory)) {
        mkdir($userAppsDirectory, 0777, true);
        logdebug('Filesystem', 'User apps directory created: ' . $userAppsDirectory);
    }

    // Creating .htaccess files
    $htaccessFile = $userDirectory . '.htaccess';
    $htaccessFileapps = $userAppsDirectory . '.htaccess';

    $htaccessContent = "
Order Deny,Allow
Deny from all
";
    file_put_contents($htaccessFile, $htaccessContent);
    logdebug('Filesystem', '.htaccess file created for user: ' . $UserID);
    file_put_contents($htaccessFileapps, $htaccessContent);
    logdebug('Filesystem', '.htaccess2 file created for user: ' . $UserID);

    if (file_exists($htaccessFile)) {
        chmod($htaccessFile, 0644);
        logdebug('Filesystem', '.htaccess file permissions set for user: ' . $UserID);
    }

    if (file_exists($htaccessFileapps)) {
        chmod($htaccessFileapps, 0644);
        logdebug('Filesystem', '.htaccess2 file permissions set for user: ' . $UserID);
    }
}


function Activationcode($activation_code, $userID)
{

    try {



        $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("INSERT INTO `activation_codes` (`id`, `user_id`, `activation_code`, `status`) VALUES (NULL, :uuid, :activcode, :states)");

        $status = 'yes';

        $stmt->bindParam(':uuid', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':activcode', $activation_code, PDO::PARAM_STR);
        $stmt->bindParam(':states', $status, PDO::PARAM_STR);

        $stmt->execute();
    } catch (PDOException $e) {
        logError($e);
        echo Format('(10) Error Email Activation', OP_Fail);
        die();
    }
}

function validatePassword($password)
{

    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter.";
    }


    if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
        return "Password must contain at least one special character.";
    }


    $length = strlen($password);
    if ($length < 8 || $length > 16) {
        return "Password must be between 8 and 16 characters long.";
    }


    return true;
}

header('Content-Type: application/json'); // Set response content type to JSON

try {
    // Create a new PDO connection
    $conn = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get JSON input
    $data = json_decode(file_get_contents('php://input'), true);
    $action = $data['action'];
    $user = $data['username'] ?? "";
    $adminkey = $data['adminkey'] ?? "";


    // if ($adminkey !== Admin_Key) {

    // }


    $stmt = $conn->prepare("SELECT sellerid FROM resellers WHERE sellerkey = :admin_key LIMIT 1");
    $stmt->bindParam(':admin_key', $adminkey, PDO::PARAM_STR);

    // Execute the query
    $stmt->execute();

    // Check if a record is found
    if (!$stmt->rowCount() > 0) {
        echo json_encode(['Fail' => 'Please check admin key.']);
        exit();
    }

    if ($action == 'add') {
        $email = EN($data['email']);


        $stmtcheck = $conn->prepare("SELECT * FROM users WHERE email = :email AND admin_key = :admkey");


        $stmtcheck->bindParam(':email', $email);
        $stmtcheck->bindParam(':admkey', $adminkey);

        $stmtcheck->execute();

        $result = $stmtcheck->fetch(PDO::FETCH_ASSOC);

        if ($result) {

            $expireDate = new DateTime($result['Expire']);
            $currentDate = new DateTime();
            if ($currentDate > $expireDate) {
                $UserID = $result['userid'];
                $sub_type = $data['subtype'];
                $Expiredate = $data['expire_date'];

                $expdate = DateTime::createFromFormat('Y-m-d', $Expiredate);

                // If parsing fails, or format is not exact, or date is not in the future -> exit.
                if (
                    !$expdate
                    || $expdate->format('Y-m-d') !== $Expiredate
                    || $expdate <= new DateTime('today')
                ) {
                    echo json_encode(['Fail' => 'Date not accepted or expired.']);
                    exit();
                }


                $stmtupdate = $conn->prepare("UPDATE users SET Expire = :exp, subtype = :subt WHERE userid = :uuid AND admin_key = :admkey");
                $stmtupdate->bindParam(':exp', $Expiredate, PDO::PARAM_STR);
                $stmtupdate->bindParam(':subt', $sub_type, PDO::PARAM_STR);
                $stmtupdate->bindParam(':uuid', $UserID, PDO::PARAM_STR);
                $stmtupdate->bindParam(':admkey', $adminkey, PDO::PARAM_STR);
                $stmtupdate->execute();


                $crypto = 'USDT';
                $cryptoAmount = $data['total_paid'];
                $extinfo = $data['additional_info'];
                $invocID = "";
                $sub_type = $data['subtype'];
                $txid = date("D M d, Y G:i");

                do {
                    $invocID = substr("" . uniqid(rand()), 0, 16);
                } while (isIDTaken($invocID));

                $result = insertPayment(
                    $UserID,
                    $crypto,
                    "success",
                    $cryptoAmount,
                    $extinfo,
                    $invocID,
                    $sub_type
                );


                updatepayment($conn, $txid, $cryptoAmount, $invocID);


                echo Format('subscription Updated.', OP_Fail);

                exit();
            } else {
                echo json_encode(['Fail' => 'this email is already in use and active.']);
                exit();
            }
        }

        $result2 = validatePassword($data['password']);
        if ($result2 !== true) {
            echo json_encode(['Fail' => $result2]);
            exit();
        }

        $pass = password_hash($data['password'], PASSWORD_DEFAULT);

        $profilePic =  'Prof.png';
        $subtype =   '12 Month';
        $authority =   'clients';
        $Expiredate = $data['expire_date']; // e.g. "2024-12-30"

        $expdate = DateTime::createFromFormat('Y-m-d', $Expiredate);

        // If parsing fails, or format is not exact, or date is not in the future -> exit.
        if (
            !$expdate
            || $expdate->format('Y-m-d') !== $Expiredate
            || $expdate <= new DateTime('today')
        ) {
            echo json_encode(['Fail' => 'Date not accepted or expired.']);
            exit();
        }

        $newid =  '';

        do {
            $newid = substr("" . uniqid(rand()), 0, 6);
        } while (isUserIDTaken($newid));

        // Insert the user data into the database
        $stmt = $conn->prepare("INSERT INTO users (userid, usrname, profilepic, email, password, Expire, subtype, authorty,admin_key) VALUES (:userid, :usrname, :profilepic, :email, :password, :expire, :subtype, :authority,:admkey)");
        $stmt->bindParam(':userid', $newid);
        $stmt->bindParam(':usrname', $user);
        $stmt->bindParam(':profilepic', $profilePic);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $pass);
        $stmt->bindParam(':expire', $Expiredate);
        $stmt->bindParam(':subtype', $subtype);
        $stmt->bindParam(':authority', $authority);
        $stmt->bindParam(':admkey', $adminkey);


        if ($stmt->execute()) {
            Activationcode('000000', $newid);

            $crypto = 'USDT';
            $cryptoAmount = $data['total_paid'];
            $extinfo = $data['additional_info'];
            $invocID = "";
            $sub_type = $data['subtype'];
            $txid = date("D M d, Y G:i");

            do {
                $invocID = substr("" . uniqid(rand()), 0, 16);
            } while (isIDTaken($invocID));

            $result = insertPayment(
                $newid,
                $crypto,
                "success",
                $cryptoAmount,
                $extinfo,
                $invocID,
                subtype_pay: $sub_type
            );


            updatepayment($conn, $txid, $cryptoAmount, $invocID);

            Creatuserfolder($newid);



            echo json_encode(['Success' => 'Account created successfully!']);
        } else {
            echo json_encode(['Fail' => 'Unable to create account.']);
        }
    } elseif ($action == 'resetid') {
        $email = EN($data['email']);


        $stmtcheck = $conn->prepare("SELECT * FROM users WHERE email = :email AND admin_key = :admkey");


        $stmtcheck->bindParam(':email', $email);
        $stmtcheck->bindParam(':admkey', $adminkey);

        $stmtcheck->execute();

        $result = $stmtcheck->fetch(PDO::FETCH_ASSOC);

        if ($result) {

            $expire = new DateTime();

            $stmtchange = $conn->prepare("UPDATE users SET hwid = NULL WHERE email = :email AND admin_key = :admkey");

            $stmtchange->bindParam(':admkey', $adminkey);
            $stmtchange->bindParam(':email', $email);
            if ($stmtchange->execute()) {
                echo json_encode(['Success' => 'Client ID Reset successfully!']);
            } else {
                echo json_encode(['Fail' => 'Unable to Reset client.']);
            }
        } else {
            echo json_encode(['Fail' => 'cant find this email.']);
        }
    } elseif ($action == 'remove') {
        // Remove user by username
        $email = EN($data['email']);


        $stmtcheck = $conn->prepare("SELECT * FROM users WHERE email = :email AND admin_key = :admkey");


        $stmtcheck->bindParam(':email', $email);
        $stmtcheck->bindParam(':admkey', $adminkey);

        $stmtcheck->execute();

        $result = $stmtcheck->fetch(PDO::FETCH_ASSOC);

        if ($result) {

            $expire = new DateTime();
            $expireString = $expire->format('Y-m-d');
            $stmtchange = $conn->prepare("UPDATE users SET subtype = 'new' , Expire = :expire WHERE email = :email AND admin_key = :admkey");
            $stmtchange->bindParam(':expire', $expireString);
            $stmtchange->bindParam(':email', $email);
            $stmtchange->bindParam(':admkey', $adminkey);
            if ($stmtchange->execute()) {
                echo json_encode(['Success' => 'Client removed successfully!']);
            } else {
                echo json_encode(['Fail' => 'Unable to remove client.']);
            }
        } else {
            echo json_encode(['Fail' => 'cant find this email.']);
        }
    } elseif ($action == 'cexpire') {
        $email = EN($data['email']);

        $stmtcheck = $conn->prepare("SELECT * FROM users WHERE email = :email AND admin_key = :admkey");


        $stmtcheck->bindParam(':email', $email);
        $stmtcheck->bindParam(':admkey', $adminkey);

        $stmtcheck->execute();
        $result = $stmtcheck->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $nexpiredate = $data['expire_date']; //example 2024-12-30

            $expdate = DateTime::createFromFormat('Y-m-d', $nexpiredate);


            if (
                $expdate
                && $expdate->format('Y-m-d') === $nexpiredate
                && $expdate > new DateTime('today')
            ) {
                // Update the user's password
                $sql = "UPDATE users SET Expire = :nexp WHERE email = :email";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':nexp', $nexpiredate);

                if ($stmt->execute()) {
                    echo json_encode(['Success' => 'Expire Date updated successfully!']);
                } else {
                    echo json_encode(['Fail' => 'Unable to update Expire Date.']);
                }
            } else {
                // Invalid or past date.
                echo json_encode(['Fail' => 'Date not accepted or expired.']);
            }
        } else {
            echo json_encode(['Fail' => 'cant find this email.']);
        }
    } elseif ($action == 'update') {


        $email = EN($data['email']);

        $stmtcheck = $conn->prepare("SELECT * FROM users WHERE email = :email AND admin_key = :admkey");


        $stmtcheck->bindParam(':email', $email);
        $stmtcheck->bindParam(':admkey', $adminkey);

        $stmtcheck->execute();
        $result = $stmtcheck->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $result2 = validatePassword($data['password']);
            if ($result2 !== true) {
                echo json_encode(['Fail' => $result2]);
                exit();
            }

            $newPass = password_hash($data['password'], PASSWORD_DEFAULT);

            // Update the user's password
            $sql = "UPDATE users SET password = :password WHERE email = :email";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $newPass);

            if ($stmt->execute()) {
                echo json_encode(['Success' => 'Password updated successfully!']);
            } else {
                echo json_encode(['Fail' => 'Unable to update password.']);
            }
        } else {
            echo json_encode(['Fail' => 'cant find this email.']);
        }
    }
} catch (Exception  $e) {
    echo json_encode(['Fail' => "Error: " . $e->getMessage()]);
}

$conn = null;
