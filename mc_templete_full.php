<?php

    /**
    * Minecraft server status fallback class
    * Read the simple server info wich are actually for minecraft clients
    * @author Patrick K. - http://www.silexboard.org/ - https://github.com/NoxNebula
    * @license GNU Public Licence - Version 3
    * @copyright c 2011-2013 Patrick K.
    */
    /**
    * Minecraft Server Status Query
    * @author Julian Spravil <julian.spr@t-online.de> https://github.com/FunnyItsElmo
    * @license Free to use but dont remove the author, license and copyright
    * @copyright c 2013 Julian Spravil
    * edited by pcchou.
    */
    /**
    * merged by mmis1000
    */
    class MinecraftServerStatusSimple {
        private $Socket;
        private $Info = array();
        /**
         * Read the minecraft server info and parse it
         * @param string $Host
         * @param int $Port optional
         * @param int $Timeout optional
         */
        public function __construct($Host, $Port = 25565, $Timeout = 3) {
            $OriginalHost = $Host;
            /* merge start*/
            //Transform domain to ip address.
            if (substr_count($Host , '.') != 4) $Host = gethostbyname($Host);
            //Get timestamp for the ping
            $start = microtime(true);
            //Connect to the server
            if(!$socket = @stream_socket_client('tcp://'.$Host.':'.$Port, $errno, $errstr, $Timeout)) {
                $this->Info['online'] = false;
            } else {
                stream_set_timeout($socket, $Timeout);
                //Write and read data
                fwrite($socket, "\xFE\x01");
                $data = fread($socket, 2048);
                fclose($socket);
                if($data == null) return false;
                //Calculate the ping
                $ping = round((microtime(true)-$start)*1000);
                //Evaluate the received data
                if (substr((String)$data, 3, 5) == "\x00\xa7\x00\x31\x00"){
                    $result = explode("\x00", mb_convert_encoding(substr((String)$data, 15), 'UTF-8', 'UCS-2'));
                    $motd = preg_replace("/(§.)/", "",$result[1]);
                }else{
                    $result = explode('§', mb_convert_encoding(substr((String)$data, 3), 'UTF-8', 'UCS-2'));
                    $motd = "";
                    foreach ($result as $key => $string) {
                        if($key != sizeof($result)-1 && $key != sizeof($result)-2 && $key != 0) {
                            $motd .= '§'.$string;
                        }
                    }
                    $motd = preg_replace("/(§.)/", "", $motd);
                }
                //Remove all special characters from a string
                /*$motd = preg_replace("/[^[:alnum:][:punct:] ]/", "", $motd); tag off due to unicode char*/
                //Set variables
                $this->Info['hostip'] = $OriginalHost;
                $this->Info['version'] = $result[0];
                $this->Info['hostname'] = $motd;
                $this->Info['numplayers'] = $result[sizeof($result)-2];
                $this->Info['maxplayers'] = $result[sizeof($result)-1];
                $this->Info['ping'] = $ping;
                $this->Info['online'] = true;
            }
            /*merge end*/
        }
        /**
         * Return the value of an key or the whole server info
         * @param string $Key optional
         * @return mixed
         */
        public function Get($Key = '') {
            return $Key ? (array_key_exists($Key, $this->Info) ? $this->Info[$Key] : false) : $this->Info;
        }
    }
    
    /**
    * Minecraft server status class
    * Query minecraft server
    * @author Patrick K. - http://www.silexboard.org/ - https://github.com/NoxNebula
    * @license GNU Public Licence - Version 3
    * @copyright c 2011-2013 Patrick K.
    */
    class MinecraftServerStatus {
        // Get the server status
        const STATUS = 0x00;
        // Make the challenge (handshake)
        const HANDSHAKE = 0x09;
        // "Magic bytes"
        const B1 = 0xFE;
        const B2 = 0xFD;
        private $Socket;
        // Expected server info (Minecraft 1.3.2)
        // more keys may added while running the code
        private $Info = array(
                'hostname' => '',
                'gametype' => '',
                'game_id' => '',
                'version' => '',
                'plugins' => '',
                'map' => '',
                'numplayers' => '',
                'maxplayers' => '',
                'hostport' => '',
                'hostip' => '',
                'ping'=>''/*addtion ping code by mmis1000*/
        );
        /**
         * Query a minecraft server and parse the status
         * @param string $Host
         * @param int $Port optional
         * @param int $Timeout optional
         */
        public function __construct($Host, $Port = 25565, $Timeout = 1) {
            /* Connect to the host and creat a socket */
            $startTime = microtime(true);/*addtion ping code by mmis1000*/
            $this->Socket = @stream_socket_client('udp://'.$Host.':'.(int)$Port, $ErrNo, $ErrStr, $Timeout);
            if(!$ErrNo && !$this->Socket === false) {
                stream_set_timeout($this->Socket, $Timeout);
                /* Make handshake and request server status */
                $Data = $this->Send(self::STATUS, pack('N', $this->Send(self::HANDSHAKE)).pack('c*', 0x00, 0x00, 0x00, 0x00));
                $ping = round((microtime(true)-$startTime)*1000);/*addtion ping code by mmis1000*/
                //set_time_limit($met);
                /*change them to here for the host only support tcp stream*/
            } else {
                $Data = false;
            }

            // Try fallback if query is not enabled on the server
            if(!$Data){
                if(!class_exists('MinecraftServerStatusSimple') && file_exists('MinecraftServerStatusSimple.class.php'))
                    require_once('MinecraftServerStatusSimple.class.php');
                if(class_exists('MinecraftServerStatusSimple')) {
                        $Fallback = new MinecraftServerStatusSimple($Host, $Port, $Timeout);
                        $this->Info = array(
                            'hostname' => $Fallback->Get('hostname'),
                            'numplayers' => $Fallback->Get('numplayers'),
                            'maxplayers' => $Fallback->Get('maxplayers'),
                            'version' => $Fallback->Get('version'),/*modified by mmis1000; add hook for mc version*/
                            'hostport' => (int)$Port,
                            'hostip' => $Host,
                            'ping' => $Fallback->Get('ping'),/*addtion ping code by mmis1000*/
                            'online' => $Fallback->Get('online')
                        ); fclose($this->Socket); return;
                } else {
                            $this->Info['online'] = false;
                            return;
                }
            }
            /* Prepare the data for parsing */
            // Split the data string on the player position
            $Data = explode("\00\00\01player_\00\00", $Data);
            // Save the players
            $Players = '';
            if($Data[1])
                $Players = substr($Data[1], 0, -2);
            // Split the server infos (status)
            $Data = explode("\x00", $Data[0]);
            /* Parse server info */
            for($i = 0; $i < sizeof($Data); $i += 2) {
                // Check if the server info is expected, if yes save the value
                if(array_key_exists($Data[$i], $this->Info) && array_key_exists($i+1, $Data))
                    $this->Info[$Data[$i]] = $Data[$i+1];
            }
            // Parse plugins and try to determine the server software
            if($this->Info['plugins']) {
                $Data = explode(": ", $this->Info['plugins']);
                $this->Info['software'] = $Data[0];
                if(isset($Data[1]))
                    $this->Info['plugins'] = explode('; ', $Data[1]);
                else
                    unset($this->Info['plugins']);
            } else {
                // It seems to be a vanilla server
                $this->Info['software'] = 'Vanilla';
                unset($this->Info['plugins']);
            }

            // Parse players
            if($Players)
                $this->Info['players'] = explode("\00", $Players);

            // Cast types
            $this->Info['numplayers'] = (int)$this->Info['numplayers'];
            $this->Info['maxplayers'] = (int)$this->Info['maxplayers'];
            $this->Info['hostport'] = (int)$this->Info['hostport'];
            $this->Info['ping'] = $ping;/*addtional ping code by mmis1000*/

            $this->Info['online'] = true;
            /* Close the connection */
            fclose($this->Socket);
        }
        /**
         * Return the value of an key or the whole server info
         * @param string $Key optional
         * @return mixed
         */
        public function Get($Key = '') {
            return $Key ? (array_key_exists($Key, $this->Info) ? $this->Info[$Key] : false) : $this->Info;
        }

        /**
         * Send a command to the server and get the answer
         * @param byte $Command
         * @param byte $Addition optional
         * @return string
         */
        private function Send($Command, $Addition = '') {
            // pack the command into a binary string
            $Command = pack('c*', self::B1, self::B2, $Command, 0x01, 0x02, 0x03, 0x04).$Addition;
            // send the binary string to the server
            if(strlen($Command) !== @fwrite($this->Socket, $Command, strlen($Command)))
                throw new Exception('Failed to write on socket', 2);
            // listen what the server has to say now
            $Data = fread($this->Socket, 2048);
            if($Data === false)
                throw new Exception('Failed to read from socket', 3);
            // remove the first 5 unnecessary bytes (0x00, 0x01, 0x02, 0x03, 0x04) Status type and own ID token
            return substr($Data, 5);
        }
    }
    /**
    * Minecraft Server Status Query
    * @author Julian Spravil <julian.spr@t-online.de> https://github.com/FunnyItsElmo
    * @license Free to use but dont remove the author, license and copyright
    * @copyright c 2013 Julian Spravil
    * edited by pcchou.
    */
    
    
    /**
    * drawString
    * draw string on image with given color, position, align, shadow
    * @Auther : mmis1000
    * @license : GNU Public Licence - Version 3
    */
    function drawString ($image, $text, $color, $size, $font, $x, $y, $xAlign = 'left', $yAlign = 'top', $shadowColor = false) {
        try {
            $type_space = imagettfbbox($size, 0, $font, $text);
            $leftPadding = $type_space[0];
            $rightPadding = $type_space[2];
            $topPadding =  $type_space[7];
            $bottomPadding =  $type_space[1];
            $height = $bottomPadding - $topPadding;
            $width = $rightPadding - $leftPadding;
            switch ($xAlign) {
                case 'left' :
                    $xShift = -$leftPadding;
                    break;
                case 'mid' :
                case 'middle' :
                    $xShift = -($leftPadding + $rightPadding) / 2;
                    break;
                case 'right' :
                    $xShift = -$rightPadding;
                    break;
                default :
                    throw new Exception('Unknown xAlign : ' . $xAlign);
            }
            switch ($yAlign) {
                case 'top' :
                    $yShift = -$topPadding;
                    break;
                case 'mid' :
                case 'middle' :
                    $yShift = -($topPadding + $bottomPadding) / 2;
                    break;
                case 'bottom' :
                    $yShift = -$bottomPadding;
                    break;
                default :
                    throw new Exception('Unknown yAlign : ' . $yAlign);
            }
            $realX = $x + $xShift;
            $realY = $y + $yShift;
            if ($shadowColor) {
                imagettftext($image, $size, 0, $realX + 1, $realY + 1, $shadowColor, $font, $text);
            }
            imagettftext($image, $size, 0, $realX, $realY, $color, $font, $text);
            return true;
        } catch (Exception $e) {
            die( 'Caught exception: ' . $e->getMessage() . "\n");
        }
    }
    
    /**
    * loadImage
    * load image by name or type
    * @Auther : mmis1000
    * @license : GNU Public Licence - Version 3
    */
        function loadImage($imgname, $type=false)
    {
        /* Decide file type if not assigned*/
        if(!$type) {
            $temp = array();
            if (preg_match("/\.(jpg|jpeg|png|gif)$/", strtolower($imgname), $temp)) {
                $type = $temp[1]; 
            }
        }
        $type = strtolower($type);
        /* Attempt to open */
        switch($type){
            case 'png' :
                $im = @imagecreatefrompng($imgname);
                break;
            case 'jpeg' :
            case 'jpg' :
                $im = @imagecreatefromjpeg($imgname);
                break;
            case 'gif' :
                $im = @imagecreatefromgif($imgname);
                break;
            default :
                if (!$type) {
                    throw new Exception('Unknown image type at file : ' . $imgname);
                } else {
                    throw new Exception('Unknown image type : ' . $type);
                }
        }
        /* See if it failed */
        if(!$im)
        {
            /* Create a blank image */
            $im  = imagecreatetruecolor(150, 30);
            $bgc = imagecolorallocate($im, 255, 255, 255);
            $tc  = imagecolorallocate($im, 0, 0, 0);

            imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

            /* Output an error message */
            imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
        }
        return $im;
    }
    /**
    * status_image_factory
    * create minecraft status image by config set and templete
    * @Auther : mmis1000
    * @require : function drawString
    * @require : function loadImage
    * @require file : gd compitable font file for text
    * @require file : image templete for status image
    * @license : GNU Public Licence - Version 3
    */
    class status_image_factory
    {
        private $Config;
        private $ImageList = array();
        private $ColorLisr = array();
        public function __construct($configSet = false)
        {
            if (!$configSet) {
                $this->Config = array (
                    'sizeX' => 320,
                    'sizeY' => 80,
                    'online' => array(
                        'overlay' => array(
                            array(
                                'fromX' => 0,
                                'fromY' => 0,
                                'width' => 320,
                                'height' => 80,
                                'toX' => 0,
                                'toY' => 0
                            ),
                            array(
                                'fromX' => 0,
                                'fromY' => 180,
                                'width' => 320,
                                'height' => 20,
                                'toX' => 0,
                                'toY' => 0
                            )
                        ),
                        'message' => array(
                            array(
                                'type' => 'text',/*can be 'count', 'text'*/
                                'statusName' => array('hostname'),
                                'onFalse' => 'replace',/*action when info is '' or false, can be 'replace' or 'none'*/
                                'replaceMessage' => array('A minecraft Server'),
                                'format' => '%s',/*always use %s to format string*/
                                'size' => 12,/*font size in pixel*/
                                'color' => '#eeeeee',
                                'x' => 290,
                                'y' => 10,
                                'xAlign' => 'right',/*left mid right*/
                                'yAlign' => 'mid'/*top mid bottom*/
                            ),
                            array(
                                'type' => 'text',
                                'statusName' => array('hostip'),
                                'onFalse' => 'replace',
                                'replaceMessage' => array('Error'),
                                'format' => 'Server ip : %s',
                                'size' => 10,
                                'color' => '#000000',
                                'x' => 310,
                                'y' => 30,
                                'xAlign' => 'right',
                                'yAlign' => 'mid'
                            ),
                            array(
                                'type' => 'text',
                                'statusName' => array('numplayers', 'maxplayers'),
                                'onFalse' => 'replace',
                                'replaceMessage' => array('N/A', 'N/A'),
                                'format' => 'Players : %s / %s',
                                'size' => 10,
                                'color' => '#000000',
                                'x' => 310,
                                'y' => 50,
                                'xAlign' => 'right',
                                'yAlign' => 'mid'
                            ),
                            array(
                                'type' => 'text',
                                'statusName' => array('ping'),
                                'onFalse' => 'none',
                                'replaceMessage' => array('N/A'),
                                'format' => 'Pings : %s ms',
                                'size' => 10,
                                'color' => '#000000',
                                'x' => 310,
                                'y' => 70,
                                'xAlign' => 'right',
                                'yAlign' => 'mid'
                            ),
                            array(
                                'type' => 'text',
                                'statusName' => array(),
                                'onFalse' => 'replace',
                                'replaceMessage' => array(),
                                'format' => 'Powered by mmis1000',
                                'size' => 8,
                                'color' => '#888888',
                                'x' => 5,
                                'y' => 70,
                                'xAlign' => 'left',
                                'yAlign' => 'mid'
                            )
                        )
                    ),
                    'offline' => array(
                        'overlay' => array(
                            array(
                                'fromX' => 0,
                                'fromY' => 80,
                                'width' => 320,
                                'height' => 80,
                                'toX' => 0,
                                'toY' => 0
                            ),
                            array(
                                'fromX' => 0,
                                'fromY' => 180,
                                'width' => 320,
                                'height' => 20,
                                'toX' => 0,
                                'toY' => 0
                            )
                        ),
                        'message' => array(
                            array(
                                'type' => 'text',/*can be 'count', 'text'*/
                                'statusName' => array('hostname'),
                                'onFalse' => 'replace',/*action when info is '' or false, can be 'replace' or 'none'*/
                                'replaceMessage' => array('A minecraft Server'),
                                'format' => '%s',/*always use %s to format string*/
                                'size' => 12,/*font size in pixel*/
                                'color' => '#eeeeee',
                                'x' => 290,
                                'y' => 12,
                                'xAlign' => 'right',/*left mid right*/
                                'yAlign' => 'mid'/*top mid bottom*/
                            ),
                            array(
                                'type' => 'text',
                                'statusName' => array('hostip'),
                                'onFalse' => 'replace',
                                'replaceMessage' => array('Error'),
                                'format' => 'Server ip : %s',
                                'size' => 10,
                                'color' => '#000000',
                                'x' => 310,
                                'y' => 40,
                                'xAlign' => 'right',
                                'yAlign' => 'mid'
                            ),
                            array(
                                'type' => 'text',
                                'statusName' => array(),
                                'onFalse' => 'replace',
                                'replaceMessage' => array(),
                                'format' => 'Powered by mmis1000',
                                'size' => 8,
                                'color' => '#888888',
                                'x' => 5,
                                'y' => 70,
                                'xAlign' => 'left',
                                'yAlign' => 'mid'
                            )
                        )
                    ),
                    'statusIcon' => array(
                        'fromX' => 0,
                        'fromY' => 160,
                        'toX' => 300,
                        'toY' => 0,
                        'height' => 20,
                        'width' => 20,
                        'useVertical' => false
                    )
                );
            } else {
                $this->Config = $configSet;
            }
        }
        public static function decodeColor ($image, $color)
        {
            $color = strtolower(preg_replace('/\s+/', '', $color));
            $catched = array();
            $match = false;
            if (preg_match("/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/", $color, $catched)) {
                $red = intval($catched[1], 16);
                $green = intval($catched[2], 16);
                $blue = intval($catched[3], 16);
                $match = true;
            }
            if (preg_match("/^rgb\((\d{1,3}),(\d{1,3}),(\d{1,3})\)$/", $color, $catched)) {
                $red = intval($catched[1], 10);
                $green = intval($catched[2], 10);
                $blue = intval($catched[3], 10);
                $match = true;
            }
            if (preg_match("/^rgb\((\d{1,3})%,(\d{1,3})%,(\d{1,3})%\)$/", $color, $catched)) {
                $red = (int)(intval($catched[1], 16) / 100 * 256);
                $green = (int)(intval($catched[2], 16) / 100 * 256);
                $blue = (int)(intval($catched[3], 16) / 100 * 256);
                $match = true;
            }
            if ($match) {
                $red = ($red > 255) ? 255 : (($red < 0) ? 0 : $red);
                $green = ($green > 255) ? 255 : (($green < 0) ? 0 : $green);
                $blue = ($blue > 255) ? 255 : (($blue < 0) ? 0 : $blue);
                return imagecolorallocate($image, $red, $green, $blue);
            }
            return false;
        }
        public function getPng($status, $fontFile, $templete, $output = false)
        {
            $image_templete = loadImage($templete);
            
            $image_status = imagecreatetruecolor($this->Config['sizeX'], $this->Config['sizeY']);
            imagesavealpha($image_status, true);
            /*start set tranparent background*/
            imagealphablending($image_status, false);
            $col=imagecolorallocatealpha($image_status,0,0,0,127);
            imagefill($image_status,0,0,$col);
            imagealphablending($image_status,true);
            /*end set tranparent background*/
            if ($status['online']) {
                $layouts = $this->Config['online'];
            } else {
                $layouts = $this->Config['offline'];
            }
            /* start for overlay */
            $overlays = $layouts['overlay'];
            foreach ($overlays as $overlay) {
                imagecopy(
                    $image_status, 
                    $image_templete, 
                    $overlay['toX'], 
                    $overlay['toY'], 
                    $overlay['fromX'], 
                    $overlay['fromY'], 
                    $overlay['width'], 
                    $overlay['height']
                );
            }
            /* end for overlay */
            /* start for text*/
            $messages = $layouts['message'];
            foreach ($messages as $message) {
                $selectedState = array();
                $ignore = false;
                for ($i = 0; $i < count($message['statusName']); $i++) {
                    $singleStateName = $message['statusName'][$i];
                    if (
                        isset($status[$singleStateName]) 
                        && $status[$singleStateName] !== false 
                        && $status[$singleStateName] !== ''
                    ) {
                        if ($message['type'] == 'text') {
                            $selectedState[] = $status[$singleStateName];
                        } else if  ($message['type'] == 'count') {
                            $selectedState[] = count($status[$singleStateName]);
                        } else {
                            throw new Exception('Unknown text handle mode : ' . $message['type']);
                        }
                    } else {
                        if ($message['onFalse'] == 'none') {
                            $ignore = true;
                            break;
                        } else if ($message['onFalse'] == 'replace') {
                        $selectedState[] = $message['replaceMessage'][$i];
                        }
                    }
                }
                if (!$ignore){
                    $finalMessage = vsprintf($message['format'], $selectedState);
                    drawString (
                        $image_status,
                        $finalMessage,
                        self::decodeColor($image_status, $message['color']),
                        $message['size'],
                        $fontFile,
                        $message['x'],
                        $message['y'],
                        $message['xAlign'],
                        $message['yAlign'],
                        (isset($message['shadow']) ? self::decodeColor($image_status, $message['shadow']) : false)
                    );
                }
            }
            /* end for text*/
            /* start for status icon*/
            /*status 0 0~60ms 1 60~200ms 2 200~500ms 3 500~1000ms 4 1000ms~ 5 offline 6 unknown*/
            if ($status['online']) {
                if (!isset($status['ping']) || isset($status['ping']) === false) {
                    $statusLevel = 6;
                } else if ($status['ping'] <= 60) {
                    $statusLevel = 0;
                } else if ($status['ping'] <= 200) {
                    $statusLevel = 1;
                } else if ($status['ping'] <= 500) {
                    $statusLevel = 2;
                } else if ($status['ping'] <= 1000) {
                    $statusLevel = 3;
                } else {
                    $statusLevel = 4;
                }
            } else {
                $statusLevel = 5;
            }
            $statusIconSet = $this->Config['statusIcon'];
            if (!$statusIconSet['useVertical']) {
                imagecopy(
                    $image_status, 
                    $image_templete, 
                    $statusIconSet['toX'], 
                    $statusIconSet['toY'], 
                    ($statusIconSet['fromX'] + $statusLevel * $statusIconSet['width']), 
                    $statusIconSet['fromY'], 
                    $statusIconSet['width'], 
                    $statusIconSet['height']
                );
            } else {
                imagecopy(
                    $image_status, 
                    $image_templete, 
                    $statusIconSet['toX'], 
                    $statusIconSet['toY'], 
                    $statusIconSet['fromX'], 
                    ($statusIconSet['fromY'] + $statusLevel * $statusIconSet['height']), 
                    $statusIconSet['width'], 
                    $statusIconSet['height']
                );
            }
            /* end for status icon*/
            
            //*註解開關
            if (!$output) {
                imagepng($image_status);
            } else {
                imagepng($image_status, $output);
            }
            imagedestroy($image_status); 
            imagedestroy($image_templete);//debug codes*/

        }
    }
    /*end of all function and class*/
    
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
    function callImage ($ip, $cache_folder, $font, $templete, $config_set = false, $cacheTime = 300, $status_handler = false) {
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
    $status_trimer = function (&$status) 
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
    
    //$ip = 'meepcraft.com';
    $default = 'bahablackbox.no-ip.org';
    $ip = isset($_GET['ip']) ? preg_replace('/[^\da-zA-Z\.]/', "", $_GET['ip']) : $default;
    $folder = 'statusCache/';
    
    //*
    require_once('mc_status_bf4_set.php');
    @callImage($ip, $folder, 'midcirc.ttf', 'templete_bf4.png', $status_bf4_set, 1800, $status_trimer);
    //*/
    
    /*
    require_once('mc_status_dark_set.php');
    callImage($ip, $folder, 'midcirc.ttf', 'templete_dark.png', $status_dark_set, 1800, $status_trimer);
    //*/