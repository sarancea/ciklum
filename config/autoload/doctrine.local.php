<?php
return array(
    'doctrine' => array(
        'connection' => array(
            'orm_default' => array(
                'driverClass' => 'Doctrine\DBAL\Driver\PDOMySQL\Driver',
                'params' => array(
                    'host' => 'localhost',
                    'user' => 'root',
                    'password' => 'qwerty',
                    'dbname' => 'zend2test',
                )
            )
        )
    )
);