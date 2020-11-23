<?php

use Spiral\Database\Driver\MySQL\MySQLDriver;

return [
  'connections' =>[
    'mysql'     => [
        'driver'  => MySQLDriver::class,
        'options' => [
          'connection' => 'mysql:host=127.0.0.1;dbname=oauth2server',
          'username'   => 'root',
          'password'   => 'aabiya@123',
          // 'timezone' => 'Asia/Kolkata'
        ]
      ],
    ],
];
