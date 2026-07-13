<?php
//prevent ddos attack
function FloodCheck($UseFor): bool
{
    $_SESSION[$UseFor] = '1';


    $_SESSION[$UseFor . '_expiration'] = time() + 3;

   
    if (isset($_SESSION[$UseFor]) && isset($_SESSION[$UseFor . '_expiration']) && time() > $_SESSION[$UseFor . '_expiration']) {
        
        unset($_SESSION[$UseFor]);
        unset($_SESSION[$UseFor . '_expiration']);
        return false;
    } else {
     
        return true;
    }
}
?>