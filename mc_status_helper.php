<?php
    
    /**
    * callImage
    * wrap for all above class with auto cache
    * @Auther : mmis1000
    * @license : GNU Public Licence - Version 3
    * @Parameter :
    * @  $ip : the server ip
    * @  $cache_folder : the folder the store cached status image
    * @  $font : the font file to use
    * @  $templete : the templete image to use
    * @  $configSet : the configSet to use in image, set it to false to use defult
    * @  $cacheTime : the time before status image was expired, defult 300 seconds
    * @  $status_handler : a function / function name that can modify the status array
    * @    Parameter :
    * @      &$status_array (pass by reference)
    */
    function status_callImage ($ip, $cache_folder, $font, $templete, $config_set = false, $cacheTime = 300, $status_handler = false) {
        if (!preg_match('/\/$/' ,$cache_folder)) {
            $cache_folder .= '/';
        }
        $file_name = 'last_' . base64_encode($ip) . '.png';
        $file_name =  str_replace(array('+', '/'), array('-', '_'), $file_name);
        $path = $cache_folder . $file_name;
        $file_time = @filemtime($path);
        if (!file_exists($cache_folder)) {
            mkdir($cache_folder, 0777, true);
        }
        if (!$file_time || (time() - filemtime($path)) > $cacheTime) {//force refresh if file expired
            $Server = new MinecraftServerStatus($ip, 25565, 3);
            $state = $Server->Get();
            if (is_callable($status_handler)) {//allow to modify the results without modify this function
                $status_handler($state);
                /*die( $state['hostname']);*/
            }
            $imagemaker = new status_image_factory($config_set);
            $imagemaker -> getPng($state, $font, $templete, $path);//redirect output to a file
            
            $file_time = @filemtime($path);//reload file time
            header("Cache-Control:no-cache");
            /*$last_modified_time = $file_time;
            $etag = base64_encode($ip);
            header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
            header("Etag: $etag"); */
        } else {//check if clinet side cache valid
            $last_modified_time = $file_time;
            $etag = base64_encode($ip);
            header("Last-Modified: ".gmdate("D, d M Y H:i:s", $last_modified_time)." GMT");
            header("Etag: $etag"); 
            if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time && 
                (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? (@trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) : true)) { 
                header("HTTP/1.1 304 Not Modified");
                return; 
            }
        }
        header('Content-Type: image/png');
        header("Content-Disposition: inline; filename=\"{$file_name}\"");
        $file=fopen($path,'rb');
        echo fread($file,filesize($path));
        fclose($file);
    }
    
    //this is a handler used to modify the result in the server status
    //這是用來修改已取得的mc伺服器狀態的函式，例如刪去多餘的行/移除mc格式碼等
    $status_modifier_default = function (&$status) 
    {
        $old = $status['hostname'];
        $old = preg_replace('/§[0-z]/', "", $old);
        $old = preg_replace('/[\n\r].+/', "", $old);
        /*this two line is used to trim unneccessory decorate text, not work very will,need redo
        $old = preg_replace('/[\-\\\/<>\{\}\[\]]+/', " ", $old);
        $old = preg_replace('/\s+/', " ", $old);
        //*/
        $status['hostname'] = $old;
        //$status['hostname'] 是伺服器名稱(motd)，想要的話也可以直接在這寫死
        //$status['hostname'] is the server name(motd), you could hardcode it here if you wish.
        return ;
    };