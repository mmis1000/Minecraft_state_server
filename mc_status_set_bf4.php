<?php
    /*optional config set*/
    $status_set_bf4 = array (
        'sizeX' => 400,
        'sizeY' => 100,
        'online' => array(
            'overlay' => array(
                array(
                    'fromX' => 0,
                    'fromY' => 0,
                    'width' => 400,
                    'height' => 100,
                    'toX' => 0,
                    'toY' => 0
                ),
                array(
                    'fromX' => 0,
                    'fromY' => 100,
                    'width' => 32,
                    'height' => 32,
                    'toX' => 211,
                    'toY' => 22
                ),
                array(
                    'fromX' => 0,
                    'fromY' => 200,
                    'width' => 400,
                    'height' => 100,
                    'toX' => 0,
                    'toY' => 0
                )
            ),
            'message' => array(
                array(
                    'type' => 'text',/*can be 'count', 'text'*/
                    'statusName' => array(),
                    'onFalse' => 'replace',/*action when info is '' or false, can be 'replace' or 'none'*/
                    'replaceMessage' => array(),
                    'format' => 'target : ',/*always use %s to format string*/
                    'size' => 8,/*font size in pixel*/
                    'color' => '#7FFF00',
                    'shadow' => '#444444',
                    'x' => 15,
                    'y' => 19,
                    'xAlign' => 'left',/*left mid right*/
                    'yAlign' => 'mid'/*top mid bottom*/
                ),
                array(
                    'type' => 'text',
                    'statusName' => array('hostip'),
                    'onFalse' => 'replace',
                    'replaceMessage' => array('Error'),
                    'format' => '%s',
                    'size' => 10,
                    'color' => '#7FFF00',
                    'shadow' => '#444444',
                    'x' => 15,
                    'y' => 33,
                    'xAlign' => 'left',
                    'yAlign' => 'mid'
                ),
                array(
                    'type' => 'text',/*can be 'count', 'text'*/
                    'statusName' => array('hostname'),
                    'onFalse' => 'replace',/*action when info is '' or false, can be 'replace' or 'none'*/
                    'replaceMessage' => array('A minecraft Server'),
                    'format' => '%s',/*always use %s to format string*/
                    'size' => 12,/*font size in pixel*/
                    'color' => '#7FFF00',
                    'shadow' => '#444444',
                    'x' => 300,
                    'y' => 65,
                    'xAlign' => 'left',/*left mid right*/
                    'yAlign' => 'mid'/*top mid bottom*/
                ),
                array(
                    'type' => 'text',
                    'statusName' => array('numplayers', 'maxplayers'),
                    'onFalse' => 'replace',
                    'replaceMessage' => array('N/A', 'N/A'),
                    'format' => 'Players : %s / %s',
                    'size' => 8,
                    'color' => '#7FFF00',
                    'shadow' => '#444444',
                    'x' => 77,
                    'y' => 80,
                    'xAlign' => 'mid',
                    'yAlign' => 'mid'
                ),
                array(
                    'type' => 'text',
                    'statusName' => array('ping'),
                    'onFalse' => 'none',
                    'replaceMessage' => array('N/A'),
                    'format' => 'Pings : %s ms',
                    'size' => 10,
                    'color' => '#7FFF00',
                    'shadow' => '#444444',
                    'x' => 300,
                    'y' => 85,
                    'xAlign' => 'left',
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
                    'y' => 95,
                    'xAlign' => 'left',
                    'yAlign' => 'mid'
                )
            )
        ),
        'offline' => array(
            'overlay' => array(
                array(
                    'fromX' => 0,
                    'fromY' => 0,
                    'width' => 400,
                    'height' => 100,
                    'toX' => 0,
                    'toY' => 0
                ),
                array(
                    'fromX' => 50,
                    'fromY' => 100,
                    'width' => 32,
                    'height' => 32,
                    'toX' => 211,
                    'toY' => 22
                ),
                array(
                    'fromX' => 0,
                    'fromY' => 300,
                    'width' => 400,
                    'height' => 100,
                    'toX' => 0,
                    'toY' => 0
                )
            ),
            'message' => array(
                array(
                    'type' => 'text',/*can be 'count', 'text'*/
                    'statusName' => array(),
                    'onFalse' => 'replace',/*action when info is '' or false, can be 'replace' or 'none'*/
                    'replaceMessage' => array(),
                    'format' => 'target : ',/*always use %s to format string*/
                    'size' => 8,/*font size in pixel*/
                    'color' => '#EA0000',
                    'shadow' => '#444444',
                    'x' => 15,
                    'y' => 19,
                    'xAlign' => 'left',/*left mid right*/
                    'yAlign' => 'mid'/*top mid bottom*/
                ),
                array(
                    'type' => 'text',
                    'statusName' => array('hostip'),
                    'onFalse' => 'replace',
                    'replaceMessage' => array('Error'),
                    'format' => '%s',
                    'size' => 10,
                    'color' => '#EA0000',
                    'shadow' => '#444444',
                    'x' => 15,
                    'y' => 33,
                    'xAlign' => 'left',
                    'yAlign' => 'mid'
                ),
                array(
                    'type' => 'text',/*can be 'count', 'text'*/
                    'statusName' => array('hostname'),
                    'onFalse' => 'replace',/*action when info is '' or false, can be 'replace' or 'none'*/
                    'replaceMessage' => array('目標遺失'),
                    'format' => '%s',/*always use %s to format string*/
                    'size' => 12,/*font size in pixel*/
                    'color' => '#EA0000',
                    'shadow' => '#444444',
                    'x' => 343,
                    'y' => 76,
                    'xAlign' => 'mid',/*left mid right*/
                    'yAlign' => 'mid'/*top mid bottom*/
                ),
                array(
                    'type' => 'text',
                    'statusName' => array(),
                    'onFalse' => 'replace',
                    'replaceMessage' => array(),
                    'format' => '人事異動',
                    'size' => 12,
                    'color' => '#EA0000',
                    'shadow' => '#444444',
                    'x' => 77,
                    'y' => 80,
                    'xAlign' => 'mid',
                    'yAlign' => 'mid'
                ),
                array(
                    'type' => 'text',
                    'statusName' => array(),
                    'onFalse' => 'replace',
                    'replaceMessage' => array(),
                    'format' => 'Powered by mmis1000',
                    'size' => 8,
                    'color' => '#666666',
                    'shadow' => '#444444',
                    'x' => 5,
                    'y' => 95,
                    'xAlign' => 'left',
                    'yAlign' => 'mid'
                )
            )
        ),
        'statusIcon' => array(
            'fromX' => 0,
            'fromY' => 150,
            'toX' => 202,
            'toY' => 13,
            'height' => 50,
            'width' => 50,
            'useVertical' => false
        )
    );