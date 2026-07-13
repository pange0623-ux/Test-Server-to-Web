<?php

//this to store values , constant
//for example any where in the code you can use DB_ServerName , because of define('DB_ServerName', '127.0.0.1')


//DB
define('DB_ServerName', '127.0.0.1');

define('DB_UserName', 'root');
define('DB_Password', "[DB-PASS]");
define('DB_Name', 'clients');

//encryption
define('Secrit_Key', '[ENCRYPT-key]');//example: MxuPz;2<6qN!>rA9.-ED@=`~VR)?wm:f
define('SIV', 'p3+.h;P/*8mv!YGF');

define('SIV_jec', 'BT-MOB!B6twG9+&U');


define('Admin_Key', 'ASDE-QEQW-BTAS-JUUZ');

define('Trusted_Agent', 'V1.BT-MOB.EvLFDEV.gmail.com');
define('Trusted_Name', 'BT-MOB');


//email
$currenthost = $_SERVER['HTTP_HOST'];
if (strpos($currenthost, '.site') !== false) {
    // Settings for the .site
    define('Email_Host', 'email.yoursite.site');
    define('My_Name', 'yoursite Support');
    define('Email_Name', 'support@yoursite.site');
    define('Email_Pass', 'xxx');//your email password
} else {
    // Default settings (.com)
    define('Email_Host', 'email.yoursite.com');
    define('My_Name', 'yoursite Support');
    define('Email_Name', 'support@yoursite.com');
    define('Email_Pass', 'xxx');//your email password
}

//slpits
define('SplitLINE', '[>L<]');
define('SplitARRAY', '[>A<]');


//imgs
define('IMG_OK', 'lime');
define('IMG_NO', 'red');
