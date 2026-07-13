<?php


//this for apk store, load download delete etc...

require_once 'yarsap_14881.php';



session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if ( !empty($data->chk) && !empty($data->subcom) && !empty($data->ran_key)) {

        try {
            $user_email = $_SESSION['session_email'] ?? 'empty';
            $user_token = $_SESSION['session_token'] ?? 'empty';
            $SubCommand = $data->subcom ?? 'empty';
            $check_key = $data->chk ?? 'empty';
            $rand_call_key = $data->ran_key ?? '';

            header('Content-Type: application/json');
            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {
                die("Authentication failed $message");
            }

            if (!isset($_SESSION['nextcheck']) || $_SESSION['nextcheck'] !== $check_key) {
                echo Format("Authentication failed 1942", OP_Fail);
                BadLogin($visitoraddress, 'joinme');
                die();
            }

            if (strlen($rand_call_key) !== 32) {
                echo Format("data is not accepted", OP_Fail);
                die();
            }



            $conn = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $stmt = $conn->prepare("SELECT userid FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");


            $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);


            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($result) {

                $userid = $result['userid'];

                try {

                    $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


                    switch ($SubCommand) {
                        case 'check':
                            try {
                                $jsonFilePath = __DIR__ . '/updates/conf.json';

                               
                                if (file_exists($jsonFilePath)) {
                                   
                                    $jsonContent = file_get_contents($jsonFilePath);
                               
                                    $data = json_decode($jsonContent, true);
                            
                                    if ($data !== null) {
                                                                           
                                        //echo json_encode($data, JSON_PRETTY_PRINT);

                                        $final_reply = EN_jector(json_encode($data),$rand_call_key);
                                        echo Format($final_reply, OP_Success);

                                    } else {
                                       // echo json_encode(["error" => "Invalid JSON format"]);
                                        echo Format("Something went wrong please try again later (0989).", OP_Fail);
                                    }
                                } else {
                                  //  echo json_encode(["error" => "Configuration file not found"]);
                                    echo Format("Something went wrong please try again later (6349).", OP_Fail);
                                }

                            } catch (PDOException $e) {
                                logError($e);
                                echo Format("Something went wrong please try again later (23).", OP_Fail);
                            }
                            break;

                        case 'download':
                           
                            
                            $filedir = __DIR__ . '/updates/update.zip';

                            if (file_exists($filedir)) {
                               
                                header('Content-Description: File Transfer');
                                header('Content-Type: application/vnd.android.package-archive'); 
                                header('Content-Disposition: attachment; filename="' . basename($filedir) . '"');
                                header('Expires: 0');
                                header('Cache-Control: must-revalidate');
                                header('Pragma: public');
                                header('Content-Length: ' . filesize($filedir));
                              
                                readfile($filedir);
                                exit();
                            } else {

                                echo Format('File not found !!!', OP_Fail);
                            }
                            break;

                        case 'delete':

                            break;
                        default:

                            break;
                    }
                } catch (PDOException $e) {
                    logError($e);
                    echo Format("Something went wrong please try again later (6).", OP_Fail);
                    exit();
                }
            } else {


                echo Format("Invalid or expired token.", OP_Fail);
            }
        } catch (PDOException $e) {
            // logError($e);
            echo Format('051 Something went wrong please try again later.', OP_Fail);
            logError($e);
        }
        $conn = null;
    } else {

        echo Format("Invalid request param.", OP_Fail);
    }
} else {

    echo Format("Invalid request.", OP_Fail);
}
function isValidApkPackageName($packageName)
{

    $pattern = '/^[a-zA-Z]([a-zA-Z0-9]*[a-zA-Z0-9]+)?(\.[a-zA-Z]([a-zA-Z0-9]*[a-zA-Z0-9]+)?)+$/';

  
    return preg_match($pattern, $packageName) === 1;
}
