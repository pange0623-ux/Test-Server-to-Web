<?php

//this called from all other php
//to check if user is login or not
//multiple check for better security

function SessionCheck($user_email, $user_token): array
{


    if (!isset($_SESSION['valid_login'])) {
        return [false, 'Login is not valid'];
    }

    if ($_SESSION['valid_login'] < time()){
        return [false, 'Login is Expired'];
    }


    if (!isset($_SESSION['ip']) || getClientIP() !== $_SESSION['ip']) {
        return [false, '(2) Login is not valid.'];
    }

    if (!isset($_SESSION['user_agent']) || ($_SERVER['HTTP_USER_AGENT'] . $_SESSION['ip']) !== $_SESSION['user_agent']) {
        return [false, '(3) Login is not valid.'];
    }


    if (!isset($_SESSION['session_email']) || $_SESSION['session_email'] !== $user_email) {
        return [false, 'Invalid Session (1).'];
    }

    if (!isset($_SESSION['session_token']) || $_SESSION['session_token'] !== $user_token) {
        return [false, 'Invalid Session (2).'];
    }

    return [true, 'K'];
}



?>
