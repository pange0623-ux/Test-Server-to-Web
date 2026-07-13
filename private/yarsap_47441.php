<?php

//this to log in server if user try to use console in browser
//F12 > console
//but i never used it.

require './yarsap_14881.php';

try {
    $json = file_get_contents('php://input');

$data = json_decode($json, true);


if ($data !== null) {

    $logs = $data['logs'] ?? die();
    $email = $data['email'] ?? die();
    $token = $data['token'] ?? die();

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


    list($isValid, $message) = SessionCheck($email, $token);

    if (!$isValid) {
        die();
    }

    $dir = 'consolelogs';
    if (!file_exists($dir)) {
        mkdir($dir);
    }

    // Generate filename (log-{datenow}.txt)
    $filename = $dir . '/log-' . date('Y-m-d') . '.txt';

    // Open or create log file
    $handle = fopen($filename, 'a');

    // Write logs to file
    foreach ($logs as $log) {
        fwrite($handle, json_encode(array("Email" => $email  , "Token" => $token , "Log" => $log)) . PHP_EOL);
    }

    // Close the file handle
    fclose($handle);

    // Send a response back to the client
    http_response_code(200);
} else {

    http_response_code(400);
}
} catch (\Throwable $th) {
    logError($th);
}


