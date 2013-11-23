<?php
    $status_dark_set = array (
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
                    'shadow' => '#444444',
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
                    'color' => '#eeeeee',
                    'shadow' => '#444444',
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
                    'color' => '#eeeeee',
                    'shadow' => '#444444',
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
                    'color' => '#eeeeee',
                    'shadow' => '#444444',
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
                    'shadow' => '#444444',
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
                    'shadow' => '#444444',
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
                    'color' => '#eeeeee',
                    'shadow' => '#444444',
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
                    'shadow' => '#444444',
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
    /*end for config set*/