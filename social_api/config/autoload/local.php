<?php
return array(
    'db' => array(
        'hostname' => 'localhost',
        'database' => 'sn',
        'port' => '3306',
        'username' => 'root',
        'password' => '',
        'adapters' => array(
            'snAdapter' => array(
                'driver' => 'Pdo_Mysql',
                'database' => 'sn',
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\\Db\\Adapter\\Adapter' => 'Zend\\Db\\Adapter\\AdapterServiceFactory',
        ),
    ),
);
