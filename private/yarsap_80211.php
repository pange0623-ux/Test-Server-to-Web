<?php

//this called every 10 sec from the panel to update user info + extra check

require_once 'yarsap_14881.php';


$visitoraddress = getClientIP();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isSus($visitoraddress, 'PhoneOnline') || isBlocked($visitoraddress)) {
    echo Format('Blocked', OP_Blocked);
    die();
}



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->email) && !empty($data->token)) {


        try {
            $user_email = $data->email ?? 'empty';
            $user_token = $data->token ?? 'empty';

            if (!FloodCheck('Refresher')) {
                echo Format("To Many Request \n Please Slow Down", OP_Fail);
                BadLogin($visitoraddress, 'Refresh');
                die();
            }

            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {
                echo Format("Authentication failed $message", OP_Fail);
                BadLogin($visitoraddress, 'Refresh');
                die();
            }



            $conn = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");


            $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);


            $stmt->execute();


            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($result) {

                $expireDate = new DateTime($result['Expire']);
                $currentDate = new DateTime();
                if ($currentDate > $expireDate) {

                    echo Format("Your subscription has expired.", OP_Fail);
                    BadLogin($visitoraddress, 'Refresh');
                } else {

                    $userId = '';
                    $baseDirectory = './user/storage/';
                    $userDirectory = $baseDirectory . $userId . '/';
                    $usedsize = '0 B';
                    $warning_storage = '0';
                    if (is_dir($userDirectory)) {
                        $size = getDirectorySize($userDirectory);
                        $usedsize = formatSize($size);
                        if ($usedsize >= 25 * 1024 * 1024 * 1024) { // 25 GB in bytes
                            $warning_storage = '1';
                        }
                    }

                    echo Format($result['usrname'] . ":" . DE($result['email']) . ":" . $result['Expire'] . ":" . $result['subtype'] . ":" . $result['userid'] . ":" . $result['profilepic'] . ":" .  $usedsize . ":" . $warning_storage, OP_Success);
                }
            } else {


                echo Format("Invalid or expired token.", OP_Fail);
                BadLogin($visitoraddress, 'Refresh');
            }
        } catch (PDOException $e) {

            logError($e);
            echo Format('709 Something went wrong please try again later.', OP_Fail);
            BadLogin($visitoraddress, 'Refresh');
        }
        $conn = null;
    } else {

        echo Format("Missing email or token data.", OP_Fail);
        BadLogin($visitoraddress, 'Refresh');
    }
} else {
    echo Format("Invalid request.", OP_Fail);
    BadLogin($visitoraddress, 'Refresh');
}
function formatSize($size)
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $i = 0;
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }

    return round($size, 2) . ' ' . $units[$i];
}
