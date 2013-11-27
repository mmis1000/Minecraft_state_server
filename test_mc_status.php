<?php
    require_once('mc_status.php');
    
    require_once('mc_status_helper.php');
    $status_modifier = $status_modifier_default;
    
    
    /*optional config sets
    require_once('mc_status_set_bf4.php');
    $config_set = $status_set_bf4;
    $templete = 'templete_bf4.png';
    //*/
    
    //*optional config sets
    require_once('mc_status_set_dark.php');
    $config_set = $status_set_dark;
    $templete = 'templete_dark.png';
    //*/
    
    
    $ip_default = '127.0.0.1';
    $ip = isset($_GET['ip']) ? preg_replace('/[^\da-zA-Z\.\-]/', "", $_GET['ip']) : $ip_default;
    $folder = 'statusCache/';
    $fontFile = 'midcirc.ttf';
    $expire_after_create = 1800;
    
    status_callImage($ip, $folder, $fontFile, $templete, $config_set, $expire_after_create, $status_modifier_default);