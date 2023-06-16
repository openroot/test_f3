<?php
require(__DIR__ . '/../vendor/autoload.php');

$f3 = \Base::instance();

$f3->AUTOLOAD = '../app/';

$f3->route('GET @index_default: /', function ($f3) {
		$f3->value1 = 'A user-defined value.';

		// URI example
		// http://localhost:4000
		echo \Template::instance()->render('/../app/segments/segment_index_default.htm');
	}
);

$f3->route('GET|POST @index_helloworld: /helloworld/@name/@age/@profession', 'operations\operation_index->helloworld_default');

$f3->run();
