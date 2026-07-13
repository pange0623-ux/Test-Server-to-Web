<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . './yarsap_8421.php';

require 'yarsap_14881.php';

function EncryptEmail($plainText) {
    $password = '4814780584699673';
    $salt = '2894356330652558';
    $iv = '2230209522049090'; // 16 bytes
    $iterations = 65536;
    $keyLength = 16; // AES-128

    // Derive key using PBKDF2 (HMAC-SHA1)
    $key = hash_pbkdf2('sha1', $password, $salt, $iterations, $keyLength, true);

    // Encrypt using AES-128-CBC
    $encrypted = openssl_encrypt($plainText, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);

    // Encode to base64 to match VB.NET output
    return base64_encode($encrypted);
}


function getusersalt($email)
{

    try {
        // Replace 'your_database_host', 'your_database_name', 'your_database_user', and 'your_database_password'
        $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare a statement to retrieve user ID based on email and token
        $stmt = $pdo->prepare('SELECT otp_salt FROM users WHERE email = :email');
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        // Fetch the user ID
        $userId = $stmt->fetchColumn();

        // Close the database connection
        $pdo = null;

        // Return the user ID if found, otherwise return null
        return ($userId !== false) ? $userId : null;
    } catch (PDOException $e) {
        logError($e);
        return null;
    }
}


function isVerified($userId)
{
    try {


        $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `activation_codes` WHERE `user_id` = :userId AND `status` = 'yes'");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return ($count > 0);
    } catch (PDOException $e) {
        logError($e);
        return false; // Return false on error
    }
}


use RobThree\Auth\TwoFactorAuth;

try {




    $expiration = date('Y-m-d H:i:s', strtotime('+1 day'));
    $domain_path = '/';
    $domain = $_SERVER['SERVER_NAME'];

    session_set_cookie_params([
        'lifetime' => 86400,

        'path' => $domain_path,
        'domain' => $domain,

        'secure' => false,

        'httponly' => false,
    ]);



    session_start();
    $visitoraddress = getClientIP();


    if (isSus($visitoraddress, 'Active') || isBlocked($visitoraddress)) {
        echo Format('Blocked', OP_Blocked);
        die();
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        try {
            $data = json_decode(file_get_contents('php://input'));
            $user_email = $data->email ?? 'empty';
            $user_pass = $data->pass ?? 'empty';
            $pin_key = $data->pkey ?? 'empty';
            $hw_id = $data->hid ?? '';
            $rand_call_key = $data->ran_key ?? '';


            sleep(5);

            if (strlen($rand_call_key) !== 32) {
                echo Format("data is not accepted", OP_Fail);
                die();
            }

            if (!FloodCheck('Activate')) {
                echo Format("To Many Request \n Please Slow Down", OP_Fail);
                die();
            }

            if (!isset($_SERVER["HTTP_USER_AGENT"])) {
                echo Format("Unsupported Browser 1", OP_Fail);
                die();
            }

            // Check if the User-Agent matches the expected format for VB.NET 

            $user_email = EN($user_email);

            if (strlen($user_pass) < 8 || !preg_match('/[A-Z]/', $user_pass) || !preg_match('/[^a-zA-Z0-9]/', $user_pass)) {

                echo Format('(1) Please Check your password.', OP_Fail);
                BadLogin($visitoraddress, 'Active');
                exit();
            }

            $user_password = $user_pass;


            $conn = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $stmt = $conn->prepare("SELECT subtype,Expire,userid,password,hwid FROM users WHERE email = :email");


            $stmt->bindParam(':email', $user_email);
            // $stmt->bindParam(':password', $user_password);

            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {

                $stord_pass = $result['password'];
                $UserID = $result['userid'];

                if (!password_verify($user_password, $stord_pass)) {
                    echo Format('invalid Email or Password.', OP_Fail);
                    BadLogin($visitoraddress, 'Active');
                    die();
                }


                if (!isVerified($UserID)) {
                    echo Format('your account is not activated.', OP_Fail);
                    BadLogin($visitoraddress, 'Active');
                    die();
                }

                $expireDate = new DateTime($result['Expire']);
                $currentDate = new DateTime();


                if ($currentDate > $expireDate) {

                    echo Format('Your subscription has expired.', OP_Fail);
                    die();
                } else {

                    $subtype = $result['subtype'];

                    if ($subtype === 'new') {
                        echo Format('account is not activated 9.', OP_Fail);
                        die();
                    }


                    $currenthwid = $result['hwid'];
                    if ($currenthwid !== null) {
                        // if ($currenthwid !== $hw_id) {
                            // echo Format('This device is not activated.', OP_Fail);
                            // die();
                        // }
                    } else {
                        $stmntbind = $conn->prepare("UPDATE users SET hwid = :nid WHERE email = :email");
                        $stmntbind->bindParam(':nid', $hw_id);
                        $stmntbind->bindParam(':email', $user_email);

                        if (!$stmntbind->execute()) {
                            echo Format('Error while logging in.', OP_Fail);
                            die();
                        }
                    }


                    $softwarename = $data->mname ?? 'empty';

                    if ($softwarename !== Trusted_Name || !str_starts_with($_SERVER["HTTP_USER_AGENT"], Trusted_Agent)) {



                        $susdata = $softwarename . "|" . $_SERVER["HTTP_USER_AGENT"];

                        $stmtcheck = $conn->prepare("SELECT * FROM users WHERE email = :email");
                        $stmtcheck->bindParam(':email', $user_email);

                        $stmtcheck->execute();

                        $result = $stmtcheck->fetch(PDO::FETCH_ASSOC);

                        if ($result) {

                            $expire = new DateTime();
                            $expireString = $expire->format('Y-m-d');
                            $stmtchange = $conn->prepare("UPDATE users SET subtype = 'new' , Expire = :expire , suspicious = :sus , token = NULL WHERE email = :email");
                            $stmtchange->bindParam(':expire', $expireString);
                            $stmtchange->bindParam(':email', $user_email);
                            $stmtchange->bindParam(':sus', $susdata);
                            if ($stmtchange->execute()) {


                                $stmtUpdateCode = $conn->prepare("UPDATE activation_codes SET status = 'no' WHERE user_id = :uuid");
                                $stmtUpdateCode->bindParam(':uuid', $UserID);
                                if ($stmtUpdateCode->execute()) {
                                    echo Format('please check your email 307.', OP_Fail);
                                } else {
                                    // Second update failed
                                    echo Format('something went wrong, try again later 08647.', OP_Fail);
                                }
                            } else {
                                echo Format('something went wrong, try again later 9613.', OP_Fail);
                            }
                        } else {
                            echo Format('please check your email 78433.', OP_Fail);
                        }
                        die();
                    }





                    $geoIpLibrary = new GeoIpLibrary('../assets/GeoIP/country.mmdb');



                    $GeoIPinfo = $geoIpLibrary->getCountryInfo($visitoraddress);

                    $visitorcountry = "Unkown";
                    $visitorISOcode = "Unkown";


                    if (!isset($GeoIPinfo['error'])) {

                        $visitorcountry = $GeoIPinfo['name'];
                        $visitorISOcode = $GeoIPinfo['isoCode'];
                    }


                    $conn2 = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                    $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $stmt2 = $conn2->prepare("SELECT * FROM users_info WHERE user_id= :usrid");
                    $stmt2->bindParam(':usrid', $UserID);

                    $stmt2->execute();
                    $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);

                    $currentpostcheck = "";

                    if ($result2) {
                        $currentpostcheck = $result2['post_check'] ?? "";
                        if (
                            $visitoraddress !== $result2['ip'] ||
                            $_SERVER['HTTP_USER_AGENT'] !== $result2['user_agent'] ||
                            $visitorISOcode !== $result2['co_code'] ||
                            $visitorcountry !== $result2['country']
                        ) {

                            echo Format('2 login invalid, please check your secret key', OP_Fail);
                            die();
                        }
                    } else {
                        echo Format('1 login invalid, please check your secret key', OP_Fail);
                        die();
                    }


                    $token = bin2hex(random_bytes(16));


                    $stmt = $conn->prepare("UPDATE users SET token = :token, token_expiration = :expiration WHERE email = :email");
                    $stmt->bindParam(':token', $token);
                    $stmt->bindParam(':expiration', $expiration);
                    $stmt->bindParam(':email', $user_email);


                    if (!$stmt->execute()) {
                        $errorInfo = $stmt->errorInfo();
                        logdebug("Active.php,updtusr", $errorInfo);
                        echo Format('(321) something went wrong , please try again later.', OP_Fail);
                        BadLogin($visitoraddress, 'Active');
                        exit();
                    }


                    setcookie('user_email', $user_email, strtotime($expiration), $domain_path);
                    setcookie('user_token', $token, strtotime($expiration), $domain_path);


                    $_SESSION['valid_login'] = time() + (6 * 60 * 60);
                    $_SESSION['session_email'] = $user_email;
                    $_SESSION['session_token'] = $token;

                    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] . $visitoraddress;
                    $_SESSION['ip'] = $visitoraddress;

                    //$randomsessionid === $pin_key


                    $postcheck = md5($user_email . ":" . $visitoraddress . ":" . $pin_key);

                    if ($currentpostcheck === $postcheck) {
                        $_SESSION['pid'] = $postcheck;

                        $nextkey = bin2hex(random_bytes(16));
                        $_SESSION['nextcheck'] = $nextkey;


                        $data = [
                            'nxtkey' => $nextkey,
                            'emil' => DE($user_email),
							'elink' => EncryptEmail($user_email),
                            'uid' => $UserID,
                            'stype' => $subtype,
                            'exp' => $result['Expire']
                        ];

                        $final_reply = EN_jector(json_encode($data),$rand_call_key);

                      

                        echo Format($final_reply, OP_Success);
                    } else {
                        echo Format('invalid Session, please check your secret key.', OP_Fail);
                        //     BadLogin($visitoraddress, 'Active');
                        exit();
                    }
                }
            } else {

                echo Format('invalid Email or Password.', OP_Fail);
                BadLogin($visitoraddress, 'Active');
                exit();
            }
        } catch (Exception $e) {
            logError($e);
            echo Format('251 Something went wrong please try again later.', OP_Fail);
        }
        $conn = null;
    } else {
        echo Format('(0) invalid Request', OP_Fail);
        BadLogin($visitoraddress, 'Active');
    }
} catch (\Throwable $th) {
    //throw $th;
    logError($th);
    echo Format('518 Something went wrong please try again later.', OP_Fail);
}
