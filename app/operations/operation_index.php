<?php
namespace operations;
use \Base as Base;
use \Template as Template;
use \transactions\transaction_f3jig as transaction_f3jig;

class operation_index {
	public function helloworld_default(Base $f3): void {
		echo '<html><head><title>Test F3</title></head><body>';
		echo '<div id="header"><h4>' . $f3->site . '</h4></div>';

		echo '<div id="content">';
		echo '<pre>This Route: ' . $f3['ALIASES.indexhelloworld'] . '</pre>';
		echo '<p>Hello World! This is a `' . $f3->VERB . '` verb.<br><br>';
		echo 'Query string posted:';
		echo '<pre>Name = ' . $f3['PARAMS.name'] . '</pre>';
		echo '<pre>Age = ' . $f3['PARAMS.age'] . '</pre>';
		echo '<pre>Profession = ' . $f3['PARAMS.profession'] . '</pre></p>';
		echo '</div>';

		echo '<div id="footer"><h4>This site is powered by <a href="http://fatfree.sourceforge.net">F3</a> - the common sense PHP framework</h4></div>';

		echo '</body></html>';
	}

	public function f3jig_default(Base $f3): void {
		$database_name = 'sample_db/';
		$table_name = 'users.json';

		$transaction_f3jig = new transaction_f3jig($f3, $database_name);
		if (isset($transaction_f3jig) && $transaction_f3jig->issuccess_init()) {
			$handle_f3jig = $transaction_f3jig->retrieve_handle();
			if (isset($handle_f3jig)) {
				$f3->index_f3jig_default = array(
					'uuid' => $handle_f3jig->uuid(),
					'dir' => $handle_f3jig->dir()
				);
			}

			$transaction_f3jig->simple_writer($table_name);
			$table_data = $transaction_f3jig->simple_reader($table_name);
			if (isset($table_data)) {
				$f3->index_f3jig_default += array('simple_table_data' => $table_data);
			}

			$f3->segment = 'segment_transaction_f3jig_default.htm';
			echo Template::instance()->render($f3->segmentappdefault);
		}
	}
}