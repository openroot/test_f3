<?php

namespace operations;

use Exception as Exception;
use \Base as Base;
use \models\abstracts\abstract_operation as abstract_operation;
use \jobs\job_rough as job_rough;
use \jobs\job_exception as job_exception;
use \jobs\job_template as job_template;
use \transactions\transaction_f3jig as transaction_f3jig;
use \transactions\transaction_f3mysql as transaction_f3mysql;
use \jobs\job_db as job_db;
use \models\enums as enums;

class operation_main extends abstract_operation {
	public function __construct() {
		parent::__construct();
	}

	// (todo): This would be a router specification.
	public function main_template_default(Base $f3): bool {
		$f3->main_template_default = [];

		$f3->main_template_default += [
			'str' => 'dev.openroot@gmail.com',
			'num' => 0420,
			'boo' => true,
			'arr_inde' => ['tues', 'wed', 'thurs', 'fri'],
			'arr_associa' => [
				'animal' => 'snake',
				'plant' => 'grass',
				'flower' => 'sunflower',
				'leaf' => 'banana'
			]
		];

		$job_template = new job_template('A Random Value', 'Another Random Value');
		$handle_template = $job_template->retrieve_handle();
		if (isset($handle_template)) { }

		$this->render();
		return true;
	}

	public function main_helloworld_getpost(Base $f3): bool {
		echo '<html><head><title>Test F3</title></head><body>';
		echo '<div id="header"><h4>' . $f3->sitename . '</h4></div>';

		echo '<div id="content">';
		echo '<pre>This Route: ' . $f3->ALIASES['main_helloworld_getpost'] . '</pre>';
		echo '<br><code>Hello World! This is a `' . $f3->VERB . '` verb.</code><br>';
		echo '<br><code>Query string posted:</code>';
		echo '<br><code>Name = ' . $f3->PARAMS['name'] . '</code>';
		echo '<br><code>Age = ' . $f3->PARAMS['age'] . '</code>';
		echo '<br><code>Profession = ' . $f3->PARAMS['profession'] . '</code>';
		echo '</div>';

		echo '<div id="footer"><h4>This site is powered by <a href="http://fatfree.sourceforge.net">F3</a> - the common sense PHP framework</h4></div>';

		echo '</body></html>';

		return true;
	}

	public function main_f3jig_default(Base $f3): bool {
		$f3->main_f3jig_default = [];

		$transaction_f3jig = new transaction_f3jig($f3);

		$handle_f3jig = $transaction_f3jig->retrieve_handle();
		if (isset($handle_f3jig)) {
			$f3->main_f3jig_default += [
				'uuid' => $handle_f3jig->uuid(),
				'dir' => $handle_f3jig->dir()
			];
		}

		$transaction_f3jig->demo_insert();
		$table_data = $transaction_f3jig->demo_select();
		if (isset($table_data)) {
			$f3->main_f3jig_default += ['sample_table_data' => $table_data];
		}

		$this->render();
		return true;
	}

	public function main_f3mysql_default(Base $f3): bool {
		$f3->main_f3mysql_default = [];

		$transaction_f3mysql = new transaction_f3mysql($f3);
		$handle_f3mysql = $transaction_f3mysql->retrieve_handle();
		if (isset($handle_f3mysql)) {
			$f3->main_f3mysql_default += ['uuid' => $handle_f3mysql->uuid()];
		}

		$transaction_f3mysql->demo_insert();
		$table_data = $transaction_f3mysql->demo_select();
		if (isset($table_data)) {
			$f3->main_f3mysql_default += ['sample_table_data' => $table_data];
		}

		$this->render();
		return true;
	}

	public function main_db_default(Base $f3): bool {
		$f3->main_db_default = [];

		$job_db = new job_db($f3, enums\enum_database_type::f3mysql);

		$handle_db = $job_db->retrieve_handle();
		if (isset($handle_db)) {
			$f3->main_db_default += ['dbtype' => enums\enum_database_type::f3mysql];
		}

		// Create a specific orm table, with optional specific orm model breadcrumb.
		$job_db->create_table('orm_cortex_sample_table', '\models\orms');

		// Create all orm tables in a directory, with optional specific orm model breadcrumb.
		$job_db->create_tables('../app/models/orms');

		$result = $job_db->f3mysql_execute('SHOW TABLES');
		if (isset($result)) {
			$f3->main_db_default += ['tables' => $result];
		}

		$this->render();
		return true;
	}
}