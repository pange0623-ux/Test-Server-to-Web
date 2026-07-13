<?php

//this for inventory in panel , where user download apk, load its own apps, delect apk

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
                echo Format("Authentication failed 2194", OP_Fail);
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

                                $querycstm = "SELECT app_package,appname,app_ico,build_date,build_state,app_ver FROM custom_app WHERE user_id=:usrid";
                                $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


                                $stmt = $pdo->prepare($querycstm);
                                $stmt->bindParam(':usrid', $userid, PDO::PARAM_INT);
                                $stmt->execute();

                                $appData = $stmt->fetchAll(PDO::FETCH_ASSOC);


                                if (empty($appData)) {
                                    echo Format("No custom apps found.", OP_Request);
                                } else {
                                    $final_reply = EN_jector(json_encode($appData),$rand_call_key);
                                    echo Format($final_reply, OP_Success);
                                }
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format("Something went wrong please try again later (17).", OP_Fail);
                            }
                            break;

                        case 'download':
                            $appid = $data->appid ?? 'empty';


                            if (!isValidApkPackageName($appid)) {
                                echo Format('Invalid Parameters(82)', OP_Fail);
                                die();
                            }

                            $appCheckQuery = "SELECT app_path FROM custom_app WHERE app_package = :appid";
                            $appCheckStmt = $pdo->prepare($appCheckQuery);
                            $appCheckStmt->bindValue(':appid', $appid, PDO::PARAM_STR);
                            $appCheckStmt->execute();
                            $resultchek = $appCheckStmt->fetch(PDO::FETCH_ASSOC);

                            if (!$resultchek) {
                                echo Format("Error: Custom App does not exist.", OP_Fail);
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

                                set_time_limit(0);

                                $size = filesize($filedir);
                                $file = fopen($filedir, 'rb');
                                if (!$file) {
                                    echo Format('Failed to open file.', OP_Fail);
                                    die();
                                }

                                $begin = 0;
                                $end = $size - 1;
                                $length = $size;

                                if (isset($_SERVER['HTTP_RANGE'])) {
                                    $range = $_SERVER['HTTP_RANGE'];
                                    list(, $range) = explode('=', $range, 2);
                                    if (strpos($range, ',') !== false) {
                                        header('HTTP/1.1 416 Requested Range Not Satisfiable');
                                        header("Content-Range: bytes $begin-$end/$size");
                                        exit;
                                    }

                                    if ($range == '-') {
                                        $begin = $size - substr($range, 1);
                                    } else {
                                        $range = explode('-', $range);
                                        $begin = intval($range[0]);

                                        if (isset($range[1]) && is_numeric($range[1])) {
                                            $end = intval($range[1]);
                                        }
                                    }

                                    $end = ($end > $size - 1) ? $size - 1 : $end;
                                    $length = $end - $begin + 1;
                                    fseek($file, $begin);
                                    header('HTTP/1.1 206 Partial Content');
                                }
                                header('Accept-Ranges: bytes');
                                header('Content-Description: File Transfer');
                                header('Content-Type: application/octet-stream');
                                header('Content-Disposition: attachment; filename="' . basename($filedir) . '"');
                                header('Expires: 0');
                                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                                header('Pragma: public');
                                header('Content-Length: ' . $length);
                                header("Content-Range: bytes $begin-$end/$size");
                                header("Content-Transfer-Encoding: binary");
                                header("Cache-Control: private", false);

                                $chunkSize = 1024 * 1024;

                                while (!feof($file) && ($p = ftell($file)) <= $end) {
                                    if ($p + $chunkSize > $end) {
                                        print fread($file, $end - $p + 1);
                                    } else {
                                        print fread($file, $chunkSize);
                                    }
                                    flush();
                                }

                                fclose($file);
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

                            $appCheckQuery = "SELECT app_path FROM custom_app WHERE app_package = :appid";
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
                                $stmt = $pdo->prepare("DELETE FROM custom_app WHERE user_id = :userid AND app_package = :appid");
                                $stmt->bindParam(':userid', $userid);
                                $stmt->bindParam(':appid', $appid);
                                $stmt->execute();
                            } catch (PDOException $e) {
                                logError($e);
                                echo Format("2 App could not be deleted from database", OP_Fail);
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
                            echo Format("Invalid request param 66.", OP_Fail);
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
