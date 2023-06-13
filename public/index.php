<?php

require(__DIR__ . '/../vendor/autoload.php');

$fff = Base::instance();
$fff->set('AUTOLOAD', '../app/');
$fff->route('GET|POST /helloworld', 'Controllers\IndexController->helloworldAction');
$fff->run();
