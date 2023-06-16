<?php
require(__DIR__ . '/../vendor/autoload.php');

$f3 = \Base::instance();

$f3->AUTOLOAD = '../app/';
$f3->DEBUG = 3;
$f3->GUI = 'gui/';

$f3->site = 'Test F3';
$f3->app = 'default';
$f3->segmentpath = '../app/segments/';
$f3->segmentappdefault = $f3->segmentpath. 'segment_app_default.htm';
$f3->segment = '';

$f3->externallink = 'window.open(this.href); return false;';

// URI example
// http://localhost:4000
$f3->route('GET @index_default: /', function ($f3) {
		$f3->segment = 'segment_index_default.htm';

		$f3->index_default_value_1 = 'A user-defined value.';

		echo \Template::instance()->render($f3->segmentappdefault);
	}
);

// URI example
// http://localhost:4000/helloworld/D Tapader/34/Software Engineer
$f3->route('GET|POST @index_helloworld: /helloworld/@name/@age/@profession', 'operations\operation_index->helloworld_default');

$f3->run();
