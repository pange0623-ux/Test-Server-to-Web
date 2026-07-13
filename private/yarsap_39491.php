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
                        case 'load':
                            try {

                                $queryapps = 'SELECT s.app_id,
                                 s.app_name,
                                  s.app_size,
                                   s.app_date,
                                    s.app_version,
                                     s.app_ico,
                                      u.build_state,
                                       u.app_ver
                                FROM store s
                                INNER JOIN user_apps u ON s.app_id = u.app_package
                                WHERE u.user_id = :userid';
                                $stmt = $pdo->prepare($queryapps);
                                $stmt->bindParam(':userid', $userid);
                                $stmt->execute();

                                

                                $appData = $stmt->fetchAll(PDO::FETCH_ASSOC);


                                if (empty($appData)) {

                                    echo Format("No store apps found.", OP_Request);

                                } else {

                                    $final_reply = EN_jector(json_encode($appData),$rand_call_key);
                                    echo Format($final_reply, OP_Success);

                                }
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format("Something went wrong please try again later (23).", OP_Fail);
                            }
                            break;

                        case 'download':
                            $appid = $data->appid ?? 'empty';


                            if (!isValidApkPackageName($appid)) {
                                echo Format('Invalid Parameters(82)', OP_Fail);
                                die();
                            }

                            $appCheckQuery = "SELECT main_activity,app_folder FROM store WHERE app_id = :appid";
                            $appCheckStmt = $pdo->prepare($appCheckQuery);
                            $appCheckStmt->bindValue(':appid', $appid, PDO::PARAM_STR);
                            $appCheckStmt->execute();
                            $resultchek = $appCheckStmt->fetch(PDO::FETCH_ASSOC);

                            if (!$resultchek) {
                                echo Format("Error: App does not exist.", OP_Fail);
                                die();
                            }


                            $baseDirectory = '../user/apps/';

                            $userDirectory = $baseDirectory . $userid . '/';
                            if (!is_dir($userDirectory)) {
                                echo Format("User Not Found", OP_Fail);
                                exit();
                            }

                            $filedir = $userDirectory . $appid . '/' . $appid . ".apk";

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


                            try {
                                $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format("185 Something went wrong, please try again later", OP_Fail);
                                die();
                            }

                            $appid = $data->appid ?? 'empty';
                            if (!isValidApkPackageName($appid)) {
                                echo Format('Invalid Parameters(904)', OP_Fail);
                                die();
                            }

                            $appCheckQuery = "SELECT main_activity,app_folder FROM store WHERE app_id = :appid";
                            $appCheckStmt = $pdo->prepare($appCheckQuery);
                            $appCheckStmt->bindValue(':appid', $appid, PDO::PARAM_STR);
                            $appCheckStmt->execute();
                            $resultchek = $appCheckStmt->fetch(PDO::FETCH_ASSOC);

                            if (!$resultchek) {
                                echo Format("Error: App does not exist.", OP_Fail);
                                die();
                            }

                            // First, try to delete the database row
                            try {
                                $stmt = $pdo->prepare("DELETE FROM user_apps WHERE user_id = :userid AND app_package = :appid");
                                $stmt->bindParam(':userid', $userid);
                                $stmt->bindParam(':appid', $appid);
                                $stmt->execute();
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format("2 App could not be deleted", OP_Fail);
                            }
							
                            $baseDirectory = '../user/apps/';

                            $userDirectory = $baseDirectory . $userid . '/';
                            if (!is_dir($userDirectory)) {
                                echo Format("User Not Found", OP_Fail);
                                exit();
                            }

                            $filedir = $userDirectory . $appid . '/' . $appid . ".apk";

                            // Now, attempt to delete the file
                            if (file_exists($filedir)) {
                                if (unlink($filedir)) {
                                    echo Format("App deleted successfully", OP_Success);
                                } else {
                                    echo Format("App could not be deleted", OP_Fail);
                                }
                            } else {
                                echo Format("App not found", OP_Fail);
                            }


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
