<?php
/*
* api.dev - config for developer PC
*/
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' =>'Doctrine\DBAL\Driver\PDOMySql\Driver',
                'params' => array(
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'root',
                    'password' => 'SafeStart!@',
                    'dbname'   => 'safe-start',
                )
            )
        )
    ),
    'safe-start-app' => array(
        'version' => '1.0',
        'baseHref' => isset($_SERVER['HTTP_HOST']) ? 'http://'.$_SERVER['HTTP_HOST'].'/api/' : './',
        'siteUrl' => isset($_SERVER['HTTP_HOST']) ? 'http://'.$_SERVER['HTTP_HOST'] : './',
        'defMenu' => array(
            'Auth',
            'Contact'
        )
    )
);
