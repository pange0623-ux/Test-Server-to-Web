<?php


//this for cloud storage
//panel > left panel > storage

require_once __DIR__ . '/../vendor/autoload.php';

use RobThree\Auth\TwoFactorAuth;


require_once 'yarsap_14881.php';

session_start();

function getusersalt($email, $token)
{

    try {
        // Replace 'your_database_host', 'your_database_name', 'your_database_user', and 'your_database_password'
        $pdo = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Prepare a statement to retrieve user ID based on email and token
        $stmt = $pdo->prepare('SELECT otp_salt FROM users WHERE email = :email AND token = :token');
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

        // Fetch the user ID
        $userId = $stmt->fetchColumn();

        // Close the database connection
        $pdo = null;

        // Return the user ID if found, otherwise return null
        return ($userId !== false) ? $userId : null;
    } catch (PDOException $e) {
        // Handle database connection errors
        logError($e);
        return null;
    }
}
function bytesConverter($bytes)
{
    $KB = 1024;
    $MB = $KB * $KB;
    $GB = $KB * $KB * $KB;
    $TB = $KB * $KB * $KB * $KB;
    $returnVal = "0 Bytes";

    if ($bytes < $KB) {
        $returnVal = $bytes . " Bytes";
    } elseif ($bytes >= $TB) {
        $returnVal = number_format($bytes / $TB, 2) . " TB";
    } elseif ($bytes >= $GB) {
        $returnVal = number_format($bytes / $GB, 2) . " GB";
    } elseif ($bytes >= $MB) {
        $returnVal = number_format($bytes / $MB, 2) . " MB";
    } elseif ($bytes >= $KB) {
        $returnVal = number_format($bytes / $KB, 2) . " KB";
    }

    return $returnVal;
}

function generateRandomFileName($length = 12)
{
    return bin2hex(random_bytes($length));
}
function generateKey()
{
    $key = bin2hex(random_bytes(16));
    return $key;
}
function clearStorageSession()
{
    if (isset($_SESSION['storage_key'])) {
        $key = $_SESSION['storage_key'];
        $_SESSION['storage_key'] = null;
        $_SESSION['storage_expiration_' . $key] = null;
    }
}
function SetStorageSession($key, $expiration)
{
    $_SESSION['storage_key'] = $key;
    $_SESSION['storage_expiration_' . $key] = $expiration;
}
function CheckStorageSession()
{
    return isset($_SESSION['storage_key']) &&
        strlen($_SESSION['storage_key']) === 32 &&
        $_SESSION['storage_expiration_' . $_SESSION['storage_key']] > time();
}

// Function to generate a unique random file name
function generateUniqueRandomFileName($directory, $length = 12)
{
    do {
        $randomName = generateRandomFileName($length);
        $filePath = $directory . $randomName;
    } while (file_exists($filePath));

    return $randomName;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'));


    if ((!empty($data->email) && !empty($data->command)) || isset($_POST['command']) && isset($_POST['email'])) {


        try {

            $comand = "null";
            $user_email = "null";

            if (isset($_POST['command']) && isset($_POST['email'])) {

                $user_email = $_POST['email'];
                $comand = $_POST['command'];
            } else {

                $user_email = $data->email ?? 'empty';
                $comand = $data->command;
            }

            $conn = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");

            $stmt->bindParam(':email', $user_email);


            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $expireDate = new DateTime($result['Expire']);
                $currentDate = new DateTime();
                if ($currentDate > $expireDate) {

                    echo "Your subscription has expired.";
                } else {
                    $userId = $result['userid'];
                    switch ($comand) {

                        case 'delete':
                            if (CheckStorageSession()) {
                                $Fids = $data->fids;
                                $IDSArray = json_decode($Fids, true);

                                try {
                                    $pdo =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                } catch (PDOException $e) {
                                    logError($e);
                                    die('Connection failed');
                                }

                                if (!is_array($IDSArray)) {
                                    $IDSArray = [$IDSArray];
                                }

                                foreach ($IDSArray as $storeid) {

                                    $selectSql = "SELECT phone_id, store_name, og_name FROM storage WHERE storeid = :storeid and user_id = :uuid";
                                    $selectStmt = $pdo->prepare($selectSql);
                                    $selectStmt->bindParam(':storeid', $storeid, PDO::PARAM_STR);
                                    $selectStmt->bindParam(':uuid', $userId, PDO::PARAM_INT);
                                    $selectStmt->execute();


                                    $row = $selectStmt->fetch(PDO::FETCH_ASSOC);

                                    if ($row) {

                                        $storeName = $row['store_name'];
                                        $ogName = $row['og_name'];

                                        $baseDirectory = '../user/storage/';
                                        $userDirectory = $baseDirectory . $userId . '/';
                                        if (!is_dir($userDirectory)) {
                                            die('User folder not found !!!');
                                        }
                                        $savesDirectory = $userDirectory . $row['phone_id'] . '-Down/';
                                        $filePath = $savesDirectory . $storeName;

                                        // if (!file_exists($filePath)) {
                                        //     die('File not found at the specified location.');
                                        // }
                                        
                                        try {
                                            // Use realpath to resolve the absolute path
                                            // $resolvedPath = realpath($filePath);
                                        
                                            // if ($resolvedPath === false) {
                                            //     die('Failed to resolve the absolute path of the file.');
                                            // }
                                        
                                            unlink($filePath);
                                        } catch (\Throwable $th) {
                                            logError($th);
                                            die('2929 something went wrong..');
                                        }

                                        $deleteSql = "DELETE FROM storage WHERE storeid = :storeid";
                                        $deleteStmt = $pdo->prepare($deleteSql);
                                        $deleteStmt->bindParam(':storeid', $storeid, PDO::PARAM_STR);
                                        $deleteStmt->execute();

                                        $rowCount = $deleteStmt->rowCount();

                                        if ($rowCount > 0) {
                                            $resultcount = [
                                                'status' => ($rowCount > 0) ? 'success' : 'not_found',
                                                'message' => ($rowCount > 0) ? "File removed: " . substr($ogName, 0, 16) . '...' : "File not found",
                                                'FID' => $storeid,
                                                'ogName' => $ogName,
                                            ];


                                            header('Content-Type: application/json');
                                            // Convert the array to a JSON string
                                            $jsonResult = json_encode($resultcount);

                                            // logdebug("Removefiles",$jsonResult);

                                            echo  $jsonResult;
                                            //  echo ">R<".$storeid.">R<"."File removed: " . substr($ogName, 0, 16).'...';
                                        } else {
                                            echo "File not found";
                                        }
                                    } else {
                                        echo "File not found for storeid = $storeid\n";
                                    }
                                }


                                $pdo = null;
                            } else {
                                echo "Storage Session Expire !!!";
                                exit;
                            }

                            break;

                        case 'download':

                            if (CheckStorageSession()) {
                                $Fids = $data->fids;

                                $IDSArray = json_decode($Fids, true);

                                try {
                                    $pdo =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                } catch (PDOException $e) {

                                    logError($e);
                                    die('Connection failed');
                                }
                                if (!is_array($IDSArray)) {
                                    $IDSArray = [$IDSArray];
                                }
                                $idString = implode(',', $IDSArray);
                                $sql = "SELECT * FROM storage WHERE storeid IN (:tofind)";
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':tofind', $idString, PDO::PARAM_STR);

                                $stmt->execute();


                                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                if ($rows && count($rows) > 0) {
                                    $baseDirectory = '../user/storage/';
                                    $userDirectory = $baseDirectory . $userId . '/';
                                    if (!is_dir($userDirectory)) {
                                        die('User folder not found !!!');
                                    }



                                    $zip = new ZipArchive();
                                    $zipFileName = $userDirectory . "SoLR_Download.zip";
                                    if ($zip->open($zipFileName, ZipArchive::CREATE) === TRUE) {
                                        foreach ($rows as $row) {
                                            $savesDirectory = $userDirectory . $row['phone_id'] . '-Down/';
                                            $fi_name = $row['store_name'];
                                            $filePath = $savesDirectory . $fi_name;
                                            $encryptedContent = file_get_contents($filePath);
                                            $decryptedContent = DE($encryptedContent);

                                            $zip->addFromString($row['og_name'], $decryptedContent);
                                        }
                                        $zip->close();


                                        header('Content-Type: application/zip');
                                        header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
                                        header('Content-Length: ' . filesize($zipFileName));
                                        readfile($zipFileName);


                                        unlink($zipFileName);


                                        exit();
                                    } else {
                                        echo "Failed to create zip archive";
                                    }
                                } else {
                                    echo 'Files not Found...';
                                    exit();
                                }

                                // Close the connection
                                $pdo = null;
                            } else {
                                echo "Storage Session Expire !!!";
                                exit;
                            }
                            break;

                        case 'Exit':

                            clearStorageSession();
                            // session_destroy();
                            echo "DONE";

                            break;
                        case 'getall':



                            if (CheckStorageSession()) {
                                //ftype
                                $Ftype = $data->ftype;


                                $whitelist = array(
                                    'images',
                                    'videos',
                                    'docs',
                                    'others'
                                );
                                if (!in_array($Ftype, $whitelist)) {
                                    echo "No Type Found 5 !!!";
                                    die();
                                }

                                $needed = null;

                                switch (strtolower($Ftype)) {

                                    case 'images':
                                        $needed = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg'];
                                        break;
                                    case 'videos':
                                        $needed = ['mp4', 'avi', 'mkv', 'mov', 'wmv', 'flv'];
                                        break;
                                    case 'docs':
                                        $needed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];
                                        break;
                                    case 'others':

                                        $needed = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'mp4', 'avi', 'mkv', 'mov', 'wmv', 'flv', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt'];

                                        break;
                                    default:
                                        break;
                                }

                                if ($needed !== null) {


                                    $phone_id = $data->pid;
                                    //echo $phone_id;

                                    $neededStr = implode("','", $needed);
                                    $neededStr = "'$neededStr'";



                                    try {
                                        $pdo3 =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                        $pdo3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                        $sql = "SELECT og_size,og_name,storeid,store_name FROM storage WHERE user_id = :user_id AND phone_id = :phone_id AND og_type IN ($neededStr)";
                                        if (strtolower($Ftype) === "others") {
                                            $sql = "SELECT og_size,og_name,storeid,store_name FROM storage WHERE user_id = :user_id AND phone_id = :phone_id AND og_type NOT IN ($neededStr)";
                                        }

                                        $stmt_get = $pdo3->prepare($sql);
                                        $stmt_get->bindParam(':user_id', $userId, PDO::PARAM_INT);
                                        $stmt_get->bindParam(':phone_id', $phone_id, PDO::PARAM_STR);
                                        $stmt_get->execute();



                                        $result_get = $stmt_get->fetchAll(PDO::FETCH_ASSOC);

                                        if ($result_get && count($result_get) > 0) {
                                            $baseDirectory = '../user/storage/';
                                            $userDirectory = $baseDirectory . $userId . '/';
                                            if (!is_dir($userDirectory)) {
                                                die('User folder not found !!!');
                                            }

                                            $savesDirectory = $userDirectory . $phone_id . '-Down/';
                                            $vidthumbs = $userDirectory . $phone_id . '-vidthumbs/';

                                            $outputArray = array();

                                            foreach ($result_get as $row) {
                                                // echo $row;
                                                $fi_name = $row['store_name'];

                                                $filePath = $savesDirectory . $fi_name;
                                                $decryptedContent = "";
                                                try{
													$encryptedContent = file_get_contents($filePath);
                                                $decryptedContent = DE($encryptedContent);
												}catch(Throwable $th){
													logError($th);
                                                    continue;
												}
                                                $fi_Dscrip = $row['og_size'] . "\n" . $row['og_name'];
                                                $fi_id = $row['storeid'];
                                                if (strtolower($Ftype) === "images") {

                                                    $base64Content = base64_encode($decryptedContent);

                                                    $data = array(
                                                        'img' => $base64Content,
                                                        'file_dis' => $fi_Dscrip,
                                                        'file_id' => $fi_id,
                                                        'file_type' => "images",
                                                    );


                                                    $outputArray[] = json_encode($data);
                                                } else if (strtolower($Ftype) === "videos") {


                                                    $sec = 1;

                                                    $ffmpeg = FFMpeg\FFMpeg::create([
                                                        'ffmpeg.binaries' => '../assets/ffmpeg/bin/ffmpeg.exe',
                                                        'ffprobe.binaries' => '../assets/ffmpeg/bin/ffprobe.exe',

                                                    ]);

                                                    $tempVideoPath = tempnam(sys_get_temp_dir(), 'video');
                                                    file_put_contents($tempVideoPath, $decryptedContent);

                                                    $video = $ffmpeg->open($tempVideoPath);
                                                    $frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($sec));
                                                    $tempThumbnailPath = tempnam(sys_get_temp_dir(), 'thumbnail');
                                                    $frame->save($tempThumbnailPath);


                                                    $thumbnailContent = file_get_contents($tempThumbnailPath);


                                                    $thumbnailBase64 = base64_encode($thumbnailContent);


                                                    unlink($tempThumbnailPath);
                                                    unlink($tempVideoPath);

                                                    $data = array(
                                                        'img' => $thumbnailBase64,
                                                        'file_dis' => $fi_Dscrip,
                                                        'file_id' => $fi_id,
                                                        'file_type' => "videos",
                                                    );

                                                    //echo '[>D<]' . $thumbnailBase64 . "<S>" . $fi_Dscrip . "<S>" . $fi_id;
                                                    $outputArray[] = json_encode($data);
                                                } else if (strtolower($Ftype) === "docs") {

                                                    $textthumb = file_get_contents('../assets/imgs/txtfile.png');
                                                    $thumbnailBase64 = base64_encode($textthumb);
                                                    $data = array(
                                                        'img' => $thumbnailBase64,
                                                        'file_dis' => $fi_Dscrip,
                                                        'file_id' => $fi_id,
                                                        'file_type' => "docs",
                                                    );

                                                    //echo '[>D<]' . $thumbnailBase64 . "<S>" . $fi_Dscrip . "<S>" . $fi_id;
                                                    $outputArray[] = json_encode($data);
                                                } else {
                                                    $othersthumb = file_get_contents('../assets/imgs/unknfile.png');
                                                    $thumbnailBase64 = base64_encode($othersthumb);
                                                    $data = array(
                                                        'img' => $thumbnailBase64,
                                                        'file_dis' => $fi_Dscrip,
                                                        'file_id' => $fi_id,
                                                        'file_type' => "others",
                                                    );

                                                    //echo '[>D<]' . $thumbnailBase64 . "<S>" . $fi_Dscrip . "<S>" . $fi_id;
                                                    $outputArray[] = json_encode($data);
                                                }
                                            }
                                            
                                            if (count($outputArray) > 0) {
                                                echo json_encode($outputArray);
                                            } else {
                                                echo "No $Ftype found 1";
                                            }
                                        } else {
                                            echo "No $Ftype found 2";
                                        }
                                    } catch (PDOException $e) {
                                        logError($e);
                                        die('Connection failed');
                                    }
                                } else {
                                    echo "No Type Found 2 !!!";
                                }
                            } else {
                                echo "Storage Session Expire !!!";
                                exit;
                            }

                            break;
                        case "getphones":
                            $usertoken = $data->token;
                            $conn2 =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                            $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                            $stmt2 = $conn2->prepare("SELECT * FROM users WHERE email = :email AND token = :tkn");

                            $stmt2->bindParam(':email', $user_email);
                            $stmt2->bindParam(':tkn', $usertoken);


                            $stmt2->execute();

                            $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
                            if (!$result2) {
                                echo "Invalid token";
                                exit;
                            }

                            $usersalt = getusersalt($user_email, $usertoken);

                            if ($usersalt === null || strlen($usersalt) === 0) {
                                echo "Please Enable Two-Factor Authentication (2FA) First.";
                                exit();
                            }

                            if (empty($data->scode)) {
                                echo "Unauthorized Access!!!";
                                exit;
                            }

                            try {

                                $otcode = $data->scode;

                                $tfa = new TwoFactorAuth();

                                $resultcode = $tfa->verifyCode(DE($usersalt), $otcode);


                                if (empty($resultcode)) {
                                    echo 'Please Check your 2FA Code.';
                                    exit;
                                } else {


                                    $conn3 =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                                    $conn3->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                    $stmt3 = $conn3->prepare("SELECT phone_id, MIN(phone_id) AS phone_id, MIN(user_id) AS user_id FROM storage WHERE user_id = :uuid GROUP BY phone_id");

                                    $stmt3->bindParam(':uuid', $userId);
                                    $stmt3->execute();

                                    $result3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);
                                    if ($result3) {
                                        $storage_key_session = generateKey();
                                        $storage_key_expiration = time() + (6 * 60 * 60);
                                        SetStorageSession($storage_key_session, $storage_key_expiration);

                                        $resultsArray = array();

                                        foreach ($result3 as $row) {
                                            $phoneIdtmp = $row['phone_id'];
                                            $conn4 =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);
                                            $conn4->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                            $stmt4 = $conn4->prepare("SELECT phone_name, address, wallpaper FROM phones WHERE phone_id = :phid");
                                            $stmt4->bindParam(':phid', $phoneIdtmp);
                                            $stmt4->execute();
                                            $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);

                                            if ($result4) {

                                                $phoneData = array(
                                                    'phone_wall' => $result4['wallpaper'],
                                                    'phone_address' => $result4['address'],
                                                    'phone_name' => $result4['phone_name'],
                                                    'phone_id' => $phoneIdtmp
                                                );


                                                $resultsArray[] = $phoneData;
                                            } else {
                                                echo json_encode(array('error' => 'No Data Found 2.'));
                                                exit;
                                            }
                                        }
                                        echo json_encode($resultsArray);
                                    } else {
                                        echo 'No Data Found.';
                                        exit;
                                    }
                                }
                            } catch (\Throwable $th) {
                                echo 'Something went wrong please try again later : x12.';
                                logError($th);
                                exit;
                            }

                            break;

                        case "save":
                            //

                            $maxFileSize = 100 * 1024 * 1024; // 100MB in bytes

                            // Allowed file extensions
                            // $allowedExtensions = array(
                            //     // Images
                            //     'jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg',

                            //     // Videos
                            //     'mp4', 'avi', 'mkv', 'mov', 'wmv', 'flv',

                            //     // Documents
                            //     'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt',

                            //     // Archives
                            //     'zip', 'rar', 'tar', 'gz', '7z',

                            //     // Audio
                            //     'mp3', 'wav', 'ogg', 'aac',

                            //     // Code
                            //     'html', 'css', 'js', 'php', 'py', 'java',

                            //     // Other
                            //     'ico', 'json', 'xml', 'csv'
                            // );



                            // define('e3s.5]v)cg/V+G#>PmDTkK2B%p<7$', 1);
                            // require_once 'yarsap_29251.php';

                            // Check if a file was sent in the POST request



                            if (!isset($_POST['myidf'])) {
                                http_response_code(403);
                                die('Forbidden');
                            }

                            $session_node = $_POST['myidf'];

                            $pdo =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


                            $stmtcheck = $pdo->prepare("SELECT * FROM nodeidfs WHERE idf = :uid AND timestamp >= NOW()");

                            $stmtcheck->bindParam(':uid',  $session_node);
                            $stmtcheck->execute();
                            $existing_id = $stmtcheck->fetchColumn();
                            if (!$existing_id) {
                                http_response_code(403);
                                die('Forbidden');
                            }






                            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                                $file = $_FILES['file'];

                                // Validate file extension
                                $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);

                                if ($file['size'] > $maxFileSize) {
                                    die('File is too large.');
                                }
                                $filesizetext = bytesConverter($file['size']);





                                $old_md5Hash = $_POST['fileverfy'];

                                $current_md5Hash = md5_file($file['tmp_name']);

                                if (strtolower($old_md5Hash) != strtolower($current_md5Hash)) {
                                    echo "This file is corrupted";
                                    exit;
                                }

                                $baseDirectory = '../user/storage/';

                                if (!is_dir($baseDirectory)) {
                                    mkdir($baseDirectory, 0777, true);
                                }

                                $userDirectory = $baseDirectory . $userId . '/';
                                if (!is_dir($userDirectory)) {
                                    mkdir($userDirectory, 0777, true);
                                }

                                $usedsize = getDirectorySize($userDirectory);

                                if ($usedsize >= 25 * 1024 * 1024 * 1024) {
                                    echo 'Your subscription store is full.';
                                    die();
                                }

                                $phone_id = $_POST['phoneid'];

                                // Define the directory for storing the file
                                $savesDirectory = $userDirectory . $phone_id . '-Down/';
                                if (!is_dir($savesDirectory)) {
                                    mkdir($savesDirectory, 0777, true);
                                }

                                $ogname = $file['name'];



                                $randomname = generateUniqueRandomFileName($savesDirectory);
                                $filePath = $savesDirectory . $randomname;

                                $fileContent = file_get_contents($file['tmp_name']);
                                $encryptedContent = EN($fileContent);
                                // file_put_contents($filePath, $encryptedContent);
                                if (file_put_contents($filePath, $encryptedContent) !== false) {


                                    $pdo =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);


                                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                    $query = "SELECT wallpaper,phone_name FROM phones WHERE phone_id = :phoneId"; // Modify the table name and structure as needed


                                    $stmt = $pdo->prepare($query);


                                    $stmt->bindParam(':phoneId', $phone_id, PDO::PARAM_STR);


                                    $stmt->execute();

                                    $result2 = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $noticon = "null";
                                    $phonename = "null";

                                    if ($result2 && $result2['wallpaper'] !== null) {
                                        $noticon = $result2['wallpaper'];
                                    } else {

                                        $noticon = "null";
                                    }
                                    if ($result2 && $result2['phone_name'] !== null) {

                                        $phonename = $result2['phone_name'];
                                    } else {
                                        $phonename = $result['authorty'];
                                    }


                                    $random_id = $current_md5Hash;
                                    $alertstate = "1";
                                    $alertmsg = " Download Complete: [" . $file['name'] . "]";
                                    $alertico = $noticon;
                                    $alertauthor = $result['authorty'];


                                    $con_stor =  new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                                    $con_stor->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                    $stmt_store = $con_stor->prepare("INSERT INTO `storage` (`user_id`, `store_name`, `phone_id`, `og_name`, `og_type`,`og_size`, `storeid`) VALUES (:userid, :ranname , :phname, :ogname, :ogtype ,:ogsize , :storeid)");
                                    $stmt_store->bindParam(':userid', $userId);
                                    $stmt_store->bindParam(':ranname', $randomname);
                                    $stmt_store->bindParam(':ogname', $ogname);
                                    $stmt_store->bindParam(':ogtype', $fileExtension);
                                    $stmt_store->bindParam(':ogsize', $filesizetext);
                                    $stmt_store->bindParam(':storeid', $random_id);
                                    $stmt_store->bindParam(':phname', $phone_id);
                                    $stmt_store->execute();

                                    if ($stmt_store->rowCount() > 0) {
                                        header('Content-Type: application/json');
                                        echo json_encode("ok");
                                        $con_stor = null;
                                    }


                                    $conn2 = new PDO("mysql:host=" . DB_ServerName . ";dbname=" . DB_Name, DB_UserName, DB_Password);

                                    $conn2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                                    $stmt2 = $conn2->prepare("INSERT INTO `alerts` (`notifi_id`, `user_id`, `state` , `alert_title` , `content`, `alert_ico`, `author`) VALUES (:alert_id, :userid, :alertstate , :title , :alertmsg, :alertico, :alertauthor)");
                                    $stmt2->bindParam(':alert_id', $random_id);
                                    $stmt2->bindParam(':userid', $userId);
                                    $stmt2->bindParam(':alertstate', $alertstate);
                                    $stmt2->bindParam(':alertmsg', $alertmsg);
                                    $stmt2->bindParam(':alertico', $alertico);
                                    $stmt2->bindParam(':alertauthor', $alertauthor);
                                    $stmt2->bindParam(':title', $phonename);
                                    $stmt2->execute();

                                    if ($stmt2->rowCount() > 0) {
                                        header('Content-Type: application/json');
                                        echo json_encode("ok");
                                    } else {
                                        header('Content-Type: application/json');
                                        echo json_encode("no");
                                    }
                                    $conn2 = null;
                                    //echo 'File stored successfully.';
                                } else {
                                    echo 'Failed to store the file.';
                                }
                            } else {
                                echo 'X777 error occurred.';
                            }
                            break;

                        // case "store":
                        //     $baseDirectory = '../user/storage/';
                        //     $imageData = $data->dataimage;
                        //     if (!is_dir($baseDirectory)) {
                        //         mkdir($baseDirectory, 0777, true);
                        //     }
                        //     $userDirectory = $baseDirectory . $userId . '/';
                        //     if (!is_dir($userDirectory)) {
                        //         mkdir($userDirectory, 0777, true);
                        //     }
                        //     $thumbsDirectory = $userDirectory . 'thumbs/';
                        //     if (!is_dir($thumbsDirectory)) {
                        //         mkdir($thumbsDirectory, 0777, true);
                        //     }
                        //     $filePath = $thumbsDirectory . 'cashfile.txt';
                        //     file_put_contents($filePath, $imageData);
                        //     echo 'ok';
                        //     break;
                        // case "load":
                        //     $baseDirectory = '../user/storage/';
                        //     $userDirectory = $baseDirectory . $userId . '/';
                        //     $thumbsDirectory = $userDirectory . 'thumbs/';
                        //     $filePath = $thumbsDirectory . 'cashfile.txt';
                        //     if (file_exists($filePath)) {
                        //         $imageData = file_get_contents($filePath);
                        //         echo $imageData;
                        //     } else {
                        //         echo 'Cash file not found.';
                        //     }
                        //     break;
                    }
                }
            } else {
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
function getDirectorySize($dir)
{
    if (!is_dir($dir)) {
        return false; // Directory doesn't exist
    }

    $size = 0;
    $files = scandir($dir);

    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (is_file($path)) {
                $size += filesize($path);
            } elseif (is_dir($path)) {
                $size += getDirectorySize($path);
            }
        }
    }

    return $size; // Return size in bytes
}
