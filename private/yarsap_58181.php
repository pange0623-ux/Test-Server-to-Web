<?php

//this called from panel to make use join the Nodejs server.


require_once 'yarsap_14881.php';


$visitoraddress = getClientIP();

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

//if (session_status() === PHP_SESSION_NONE) {
session_start();
//}


// if (isSus($visitoraddress, 'joinme') || isBlocked($visitoraddress)) {
    // echo Format('Blocked', OP_Blocked);
    // die();
// }




if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->case) && !empty($data->chk)) {


        try {
            $user_email = $_SESSION['session_email'] ?? 'empty';
            $user_token = $_SESSION['session_token'] ?? 'empty';
            $case = $data->case ?? 'empty';
            $check_key = $data->chk ?? 'empty';
            $ping_state = $data->ping ?? '0';
            $is_main_socket = $data->ismain ?? '0'; //used for first connect bettwen control and mob
            $user_idf =  'empty';

            if (
                !in_array($ping_state, ['0', '1'], true) ||
                !in_array($is_main_socket, ['0', '1'], true)
            ) {
                echo Format("Invalid request (8899).", OP_Fail);
                BadLogin($visitoraddress, 'joinme');
                die();
            }

            $allowedcase = array("join", "out");
            if (!in_array($case, $allowedcase)) {
                echo Format("Invalid request (1).", OP_Fail);
                BadLogin($visitoraddress, 'joinme');
                die();
            }

          
            sleep(1);


            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {

                echo Format("Authentication failed $message", OP_Fail);
                BadLogin($visitoraddress, 'joinme');
                die();
            }


            if (!isset($_SESSION['nextcheck']) || $_SESSION['nextcheck'] !== $check_key) {
                echo Format("Authentication failed 8732", OP_Fail);
                BadLogin($visitoraddress, 'joinme');
                die();
            }


            $conn =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            //i need to fetach 'subtype' also 
            $stmt = $conn->prepare("SELECT userid,subtype FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");


            $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);


            if ($stmt->execute()) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {

                    $userId  = $result['userid'];
                    $userSubtype = $result['subtype'];
                    $unkownsub = false;
                    $maxSubClients = '0';


                    switch ($userSubtype) {
                        case '1 Month':
                            $maxSubClients = '10';
                            break;
                        case '3 Month':
                            $maxSubClients = '20';
                            break;
                        case '12 Month':
                            $maxSubClients = '40';
                            break;
                        case 'new':
                            $unkownsub = true;
                            break;
                        default:

                            $unkownsub = true;

                            break;
                    }

                    if ($unkownsub === true) {
                        echo Format("Your account is not activiated.", OP_Fail);
                        exit();
                    }



                    $conn_idf =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                    $conn_idf->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);




                    if ($ping_state === "1") {
                        //just ping
                        $user_idf = $data->usridf ?? 'BT-MOB' . bin2hex(random_bytes(8));
                        if ($case === "out") {
                            $deletequery = "DELETE FROM nodeidfs WHERE user_id = :uuid";
                            $stmt_idf = $conn_idf->prepare($deletequery);

                            $stmt_idf->bindParam(':uuid',  $userId);
                            $stmt_idf->execute();
                        }
                    } else {
                        $deletequery = "DELETE FROM nodeidfs WHERE user_id = :uuid AND timestamp < NOW() AND ismain != '1'";


                        if ($is_main_socket === "1") {
                            $deletequery = "DELETE FROM nodeidfs WHERE user_id = :uuid AND timestamp < NOW() OR user_id = :uuid AND ismain = '1'";
                        }
                        $stmt_idf = $conn_idf->prepare($deletequery);

                        $stmt_idf->bindParam(':uuid',  $userId);
                        $stmt_idf->execute();

                        //$user_idf = uniqid('BT-MOB');
                        $user_idf = 'BT-MOB' . bin2hex(random_bytes(8)); // Generates a 16-character random hex string

                    }




                    $data = array(
                        'itype' => 'slr_panel',
                        'idf' => $user_idf,
                        'subc' => $case,
                        'maxsub' => $maxSubClients,
                        'userId' => $userId
                    );

                    $jsonData = json_encode($data);

                    $queryString = http_build_query(array('packet' => $jsonData));


                    //node will only accept request from this php keep localhost
                    $url = 'http://localhost:3000/auth?' . $queryString;
                    try {
                        $response = file_get_contents($url);
                    } catch (\Throwable $th) {
                        //throw $th;
                        logError($th);
                        echo Format("Server not available right now.", OP_Fail);
                        exit();
                    }

                    if ($response === false) {
                        echo Format("Something went wrong please try again later(3).", OP_Fail);
                        exit();
                    } else {
                        $responseData = json_decode($response, true);
                        if ($responseData !== null) {
                            // Check if the state is 'ok'
                            if ($responseData['state'] === 'ok') {

                                setcookie('user_idf', $user_idf, strtotime($expiration), $domain_path);


                                if ($ping_state !== "1") {
                                    
                                    $stmt_idf = $conn_idf->prepare("INSERT INTO nodeidfs (idf, timestamp, user_id, ismain) VALUES (:id, DATE_ADD(NOW(), INTERVAL 6 HOUR), :uuid, :ismn)");
                                } else {
                                   
                                    $stmt_idf = $conn_idf->prepare("UPDATE nodeidfs SET timestamp = DATE_ADD(NOW(), INTERVAL 6 HOUR) WHERE idf = :id");
                                }

                                $stmt_idf->bindParam(':id',  $user_idf);
                                if ($ping_state !== "1") {
                                    $stmt_idf->bindParam(':uuid', $userId);
                                    $stmt_idf->bindParam(':ismn', $is_main_socket);
                                }
                                $stmt_idf->execute();
                                //$is_main_socket


                                echo Format($user_idf, OP_Success);
                                exit();
                            } else {
                                //echo "State is not OK";
                                echo Format("Server Rejected request.", OP_Fail);
                                BadLogin($visitoraddress, 'joinme');
                                exit();
                            }
                        } else {
                            echo Format("Something went wrong please try again later.", OP_Fail);
                            exit();
                        }
                    }
                } else {
                    echo Format("Invalid or expired token.", OP_Fail);
                    BadLogin($visitoraddress, 'joinme');
                }
            } else {
                echo Format("Something went wrong , please try again later.", OP_Fail);
                BadLogin($visitoraddress, 'joinme');
            }
        } catch (PDOException $e) {
            logError($e);
            echo Format('027 Something went wrong please try again later.', OP_Fail);
            BadLogin($visitoraddress, 'joinme');
        }
        $conn = null;
       // $conn_idf = null;
    } else {

        echo Format("Missing email or token data.", OP_Fail);
        BadLogin($visitoraddress, 'joinme');
    }
} else {

    echo Format("Invalid request.", OP_Fail);
    BadLogin($visitoraddress, 'joinme');
}
