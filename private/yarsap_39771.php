<?php

//this caleld from php to check the ip flag country.

require_once __DIR__.'/../vendor/autoload.php';


require_once 'yarsap_14881.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$visitoraddress =getClientIP();
if (isSus($visitoraddress, 'ipinfo') || isBlocked($visitoraddress)) {
    die('Blocked');
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->email) && !empty($data->token) && !empty($data->phoneip)) {


        try {
            $user_email = $data->email ?? 'empty';
            $user_token = $data->token ?? 'empty';
            $phone_ip = $data->phoneip;




            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {
                echo "Authentication failed ,$message";
                BadLogin($visitoraddress, 'ipinfo');
                exit;
            }


            $conn = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $stmt = $conn->prepare("SELECT userid FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");


            $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);


            $stmt->execute();


            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($result) {
                $geoIpLibrary = new GeoIpLibrary('../assets/GeoIP/country.mmdb');
                $GeoIPinfo = $geoIpLibrary->getCountryInfo($phone_ip);


                if (isset($GeoIPinfo['error'])) {
                    //BadLogin($visitoraddress,'ipinfo');
                    echo 'Address is not Accepted: ' . $phone_ip . ' , ' . $GeoIPinfo['error'];
                    return;
                } else {
                    header('Content-Type: application/json');
                    echo json_encode($GeoIPinfo);
                }
            } else {
                // Token is invalid or expired
                echo "Invalid or expired token.";
                BadLogin($visitoraddress, 'ipinfo');
            }
        } catch (PDOException $e) {
            logError($e);
            echo Format('082 Something went wrong please try again later.', OP_Fail);
        }
        $conn = null;
    } else {
        echo "Invalid request param.";
        BadLogin($visitoraddress, 'ipinfo');
    }
} else {
    echo "Invalid request.";
    BadLogin($visitoraddress, 'ipinfo');
}
