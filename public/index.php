<?php

require(__DIR__ . '/../vendor/autoload.php');

$fff = Base::instance();
$fff->set('AUTOLOAD', '../app/');
$fff->route('GET|POST /helloworld/@name/@age/@profession', 'Controllers\IndexController->helloworldAction');
$fff->run();
