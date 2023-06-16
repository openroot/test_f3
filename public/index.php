<?php

require(__DIR__ . '/../vendor/autoload.php');

$f3 = \Base::instance();
$f3->set('AUTOLOAD', '../app/');

$f3->route('GET @index: /', function ($f3) {
		$f3->value1='A user-defined value.';
		echo \Template::instance()->render('/../app/segments/segment_sample.htm');
	}
);

$f3->route('GET|POST @index: /helloworld/@name/@age/@profession', 'operations\operation_index->helloworld_default');

$f3->run();
