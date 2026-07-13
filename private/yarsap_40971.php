<?php

//class for tools , any functions

function str_zip($originalString) : string {
$compressedString = gzcompress($originalString);
return $compressedString;
}

function str_Dzip($compressedString) : string {
    $uncompressedString = gzuncompress($compressedString);
    return $uncompressedString;
}
function PasswordGenerator($length, $islower, $isupper, $ispial, $isnum)
{
    $digits    = $isnum ? array_flip(range('0', '9')) : array("");
    $lowercase = $islower ? array_flip(range('a', 'z')) : array("");
    $uppercase = $isupper ? array_flip(range('A', 'Z')) : array("");
    $special   = $ispial ? array_flip(str_split('!@#$%^&*')) : array("");
    $combined  = array_merge($digits, $lowercase, $uppercase, $special);

    $password = array();

    $mins = 0;

    if ($isnum) {
        $mins = $mins + 1;
        $password[] = array_rand($digits);
    }


    if ($islower) {
        $mins = $mins + 1;
        $password[] = array_rand($lowercase);
    }


    if ($isupper) {
        $mins = $mins + 1;
        $password[] = array_rand($uppercase);
    }


    if ($ispial) {
        $mins = $mins + 1;
        $password[] = array_rand($special);
    }


    for ($i = 0; $i < $length - $mins; $i++) {
        $password[] = array_rand($combined);
    }

    shuffle($password);
    $password = implode($password);

    return $password;
}
function getClientIP() {
    $clientIP = '';

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipList = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $clientIP = trim($ipList[0]);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $clientIP = $_SERVER['HTTP_CLIENT_IP'];
    } else {
        $clientIP = $_SERVER['REMOTE_ADDR'];
    }

    return $clientIP;
}

?>