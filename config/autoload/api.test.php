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
                    'password' => 'Pass!@',
                    'dbname'   => 'safe_start_tests',
                )
            )
        )
    )
);
