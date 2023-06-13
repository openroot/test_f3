<?php

require(__DIR__ . '/../vendor/autoload.php');

$fff = Base::instance();
$fff->route('GET|POST /helloworld', function ($fff) {
	echo 'Hello World! This is a '.$fff->VERB.'.';
});
$fff->run();
