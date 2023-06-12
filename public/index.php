<?php

require(__DIR__ . '/../vendor/autoload.php');

$fff = Base::instance();
$fff->route('GET /', function ($fff) {
	echo "Hello World!";
});
$fff->run();
