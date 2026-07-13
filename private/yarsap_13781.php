<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . './yarsap_8421.php';

require_once 'yarsap_14881.php';

use RobThree\Auth\TwoFactorAuth;

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



try {




    $expiration = date('Y-m-d H:i:s', strtotime('+1 day'));
    $domain_path = '/';
    $domain = $_SERVER['SERVER_NAME'];

    session_set_cookie_params([
        'lifetime' => 86400,

        'path' => $domain_path,
        'domain' => $domain,

        'secure' => false,

        'httponly' => true,
    ]);


    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $visitoraddress = getClientIP();
    $csrf = new CSRF($session_name = 'Logintoken_Check', $input_name = 'Logintokens_Validator', $hashTime2Live = 300, $hashSize = 64);

    if (!$csrf->validate('LoginForm')) {
        echo Format("invalid Request \n Reload your Page", OP_Fail);
        BadLogin($visitoraddress, 'Active');
        die();
    }


    if (isSus($visitoraddress, 'Active') || isBlocked($visitoraddress)) {
        echo Format('Blocked', OP_Blocked);
        die();
    }


    if (!empty($_POST['email']) && !empty($_POST['password'])) {


        try {




            // Start or Resume a session

            // Initialize an instance

            if (!FloodCheck('Activate')) {
                echo Format("To Many Request \n Please Slow Down", OP_Fail);
                die();
            }

            // sleep(1);

            // //TODO: RECAPTCHA , also enable in login.php

            // if (isset($_POST['g-recaptcha-response'])) {
                // // RECAPTCHA SETTINGS
                // $captcha = $_POST['g-recaptcha-response'];
                // $ip = $_SERVER['REMOTE_ADDR'];


                // // Define the reCAPTCHA secret keys for both domains
                // $secretKeys = [
                    // 'padariadojoao.shop' => '6LeEEWkqAAAAAHDZ-zZNpVMiciQdjxIGf63gDScn'
                // ];

                // // Choose the appropriate secret key based on the current domain
                // $key = isset($secretKeys[$domain]) ? $secretKeys[$domain] : $secretKeys['padariadojoao.shop']; // Default to .com key if no match

                // $url = 'https://www.google.com/recaptcha/api/siteverify';

                // // RECAPTCH RESPONSE
                // $recaptcha_response = file_get_contents($url . '?secret=' . $key . '&response=' . $captcha . '&remoteip=' . $ip);
                // $data = json_decode($recaptcha_response);

                // if (!isset($data->success) || $data->success !== true) {
                    // echo Format("Please verify reCAPTCHA first 1.", OP_Fail);
                    // BadLogin($visitoraddress, 'updatepass');
                    // die();
                // }
            // } else {
                // echo Format("Please verify reCAPTCHA first 2.", OP_Fail);
                // BadLogin($visitoraddress, 'Active');
                // die();
            // }



            if (!isset($_SERVER["HTTP_USER_AGENT"])) {
                echo Format("Unsupported Browser", OP_Fail);
                die();
            }


            $user_email = EN($_POST['email']);



            if (strlen($_POST['password']) < 8 || !preg_match('/[A-Z]/', $_POST['password']) || !preg_match('/[^a-zA-Z0-9]/', $_POST['password'])) {

                echo Format('(1) Please Check your password.', OP_Fail);
                BadLogin($visitoraddress, 'Active');
                exit();
            }

            $user_password = $_POST['password'];


            $conn = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $stmt = $conn->prepare("SELECT subtype,Expire,userid,password FROM users WHERE email = :email");


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
                    echo Format('Your account is not active 1952', OP_Fail);
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
                        echo Format('Complete your payment.', OP_Request);
                        die();
                    }


                    $usersalt = getusersalt($user_email);
                    if ($usersalt != null && strlen($usersalt) > 0) {
                        if (isset($_POST['tfacode'])) {
                            $usrcode = $_POST['tfacode'];

                            $tfa = new TwoFactorAuth();

                            $resultcode = $tfa->verifyCode(DE($usersalt), $usrcode);

                            if (empty($resultcode)) {

                                echo Format('check your 2FA code', OP_Fail);
                                BadLogin($visitoraddress, 'Active');
                                exit();
                            } 
                        } else {
                            // echo Format('create 2fa firat.', OP_Request);
                            // die();
                            echo Format("2FA", OP_Request);
                            BadLogin($visitoraddress, 'Active');
                            die();
                        }
                    }else {
                        //here no 2fa created , create one
                        if (isset($_POST['tfacode'])) {
                            $usrcode = $_POST['tfacode'];

                            $tfa = new TwoFactorAuth();

                            $resultcode = $tfa->verifyCode(DE($usersalt), $usrcode);

                            if (empty($resultcode)) {

                                echo Format('check your 2FA code', OP_Fail);
                                BadLogin($visitoraddress, 'Active');
                                exit();
                            }
                        } else {

                            $token = bin2hex(random_bytes(16));
                            $tfa = new TwoFactorAuth();
                            $secret = $tfa->createSecret();
                            $domain = $_SERVER['SERVER_NAME'];
                            $qrCodeImage = $tfa->getQRCodeImageAsDataUri($domain.":" . DE($user_email), $secret);
                    
                            $sesioncheck = md5($visitoraddress . ":" . $_SERVER['HTTP_USER_AGENT'].":".$token);
                    
                            $_SESSION['sesioncheck'] = $sesioncheck;
                            $_SESSION['2FA_Skey'] = EN($secret);
                    
                            $data = [
                                'QR' => $qrCodeImage,
                                'tok' => $token,
                                'EM' => $user_email,
                                'CTFA' => "OK"
                            ];
                            echo Format($data,OP_Request);

                           // echo Format('CTFA', OP_Request);
                            die();
                            // echo Format("2FA", OP_Request);
                            // BadLogin($visitoraddress, 'Active');
                            // die();
                        }
                     
                    }



                    $geoIpLibrary = new GeoIpLibrary('../assets/GeoIP/country.mmdb');



                    $GeoIPinfo = $geoIpLibrary->getCountryInfo($visitoraddress);
                    $visitorcountry = "Unkown";
                    $visitorISOcode = "Unkown";


                    if (!isset($GeoIPinfo['error'])) {
                        
                        $visitorcountry = $GeoIPinfo['name'];
                        $visitorISOcode = $GeoIPinfo['isoCode'];
                    }


                    $stmt3 = $conn->prepare("
                        INSERT INTO users_info (info_id, user_id, co_code, country, ip, user_agent,post_check)
                         VALUES (null, :usrid, :ccode, :county, :adrs, :agent,:pchk)
                         ON DUPLICATE KEY UPDATE
                        co_code = VALUES(co_code),
                         country = VALUES(country),
                        ip = VALUES(ip),
                         user_agent = VALUES(user_agent),
                         post_check= VALUES(post_check)
                        ");

                    

                    $tagent = Trusted_Agent;
                    $randomsessionid = 
                    strtoupper(GRstr(4)).
                    '-'.
                    strtoupper(GRstr(4)).
                    '-'.
                    strtoupper(GRstr(4)).
                    '-'.
                   strtoupper(GRstr(4));

                    $postcheck = md5($user_email . ":" . $visitoraddress . ":" . $randomsessionid);


                    $stmt3->bindParam(':usrid', $UserID);
                    $stmt3->bindParam(':ccode', $visitorISOcode);
                    $stmt3->bindParam(':county', $visitorcountry);
                    $stmt3->bindParam(':adrs', $visitoraddress);
                    $stmt3->bindParam(':agent', $tagent);
                    $stmt3->bindParam(':pchk', $postcheck);
                    // $stmt3->execute();

                    if (!$stmt3->execute()) {
                        $errorInfo = $stmt3->errorInfo();
                        logdebug("Active.php,usrinfo", $errorInfo);
                        echo Format('Exception login, please try again later.', OP_Fail);
                        BadLogin($visitoraddress, 'Active');

                        exit();
                    }
                   

                    echo Format($randomsessionid, OP_Success);
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
