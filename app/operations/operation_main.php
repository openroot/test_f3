<?php

namespace operations;

use Exception as Exception;
use \Base as Base;
use \Template as Template;
use \jobs\job_exception as job_exception;
use \jobs\job_template as job_template;
use \jobs\job_db as job_db;
use \jobs\job_rough as job_rough;
use \transactions\transaction_f3jig as transaction_f3jig;
use \transactions\transaction_f3mysql as transaction_f3mysql;
use \models\enums as enums;

class operation_main {
	private ?string $handle_this = NULL;
	private ?Base $f3 = NULL;
	private ?Template $f3template = NULL;

	public function __construct() {
		if ($this->validate_config()) {
			return $this->handshake();
		}
		return false;
	}

	private function validate_config(): bool {
		return true;
	}

	private function handshake(): bool {
		try {
			// TODO: Put 'initialization' logics here for app client.
			$this->handle_this = 'To be replaced with real \'client\' object';
		}
		catch (Exception $exception) {
			$this->destroy_handle();
			throw new job_exception('App client unable to initialized.', $exception);
		}

		if ($this->issuccess_init()) {
			if ($this->initialize_f3singletones()) {
				// Preparing transactions, perhaps can be put in an another different operation, to avoid repeating every run main-stream.
				return $this->prepare_transactions();
			}
		}
		return false;
	}

	public function issuccess_init(): bool {
		if (isset($this->handle_this)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function retrieve_handle(): ?string {
		if (isset($this->handle_this)) {
			return $this->handle_this;
		}
		else {
			if ($this->validate_config() && $this->handshake()) {
				return $this->handle_this;
			}
		}
		return NULL;
	}

	public function destroy_handle() {
		$this->handle_this = NULL;
	}

	private function initialize_f3singletones(): bool {
		if ($this->issuccess_init()) {
			try {
				$this->f3 = Base::instance();
				$this->f3template = Template::instance();

				return true;
			}
			catch (Exception $exception) {
				$this->destroy_handle();
				throw new job_exception('F3 singletones couldn\'t be initialized.', $exception);
			}
		}
		return false;
	}

	private function prepare_transactions(): bool {
		$successchain = true;
		try {
			$job_rough = new job_rough($this->f3);

			// Preparing MySQL database, perhaps can be put in an another different operation, to avoid repeating every run main-stream.
			$job_db = new job_db($this->f3, enums\enum_database_type::f3mysql);
			if (isset($job_db) && $job_db->issuccess_init()) {
				if (!$job_rough->prepare_mysql($job_db)) {
					throw new Exception('Preparing MySQL couldn\'t be done.');
				}
			}
			else {
				throw new Exception('Database job couldn\'t be initialized.');
			}
		}
		catch (Exception $exception) {
			$successchain = false;
			throw new job_exception('Transactions couldn\'t be initialized.', $exception);
		}
		return $successchain;
	}

	// (todo): This would be a router specification.
	public function main_template_default(Base $f3): bool {
		if ($this->issuccess_init()) {

			$job_template = new job_template('A Random Value', 'Another Random Value');
			if (isset($job_template) && $job_template->issuccess_init()) {
				$handle_template = $job_template->retrieve_handle();
				if (isset($handle_template)) {
					$f3->main_template_default = array(
						'str' => 'dev.openroot@gmail.com',
						'num' => 0420,
						'boo' => true,
						'arr_inde' => array('tues', 'wed', 'thurs', 'fri'),
						'arr_associa' => array(
							'animal' => 'snake',
							'plant' => 'grass',
							'flower' => 'sunflower',
							'leaf' => 'banana'
						)
					);
				}

				$f3->segmentsrender = 'segment_operation_main_template_default.htm';
				echo $this->f3template->render($f3->segmentsdefaultrender);
			}

			return true;
		}
		return false;
	}

	public function main_helloworld_getpost(Base $f3): bool {
		if ($this->issuccess_init()) {
			echo '<html><head><title>Test F3</title></head><body>';
			echo '<div id="header"><h4>' . $f3->sitename . '</h4></div>';

			echo '<div id="content">';
			echo '<pre>This Route: ' . $f3->ALIASES['main_helloworld_getpost'] . '</pre>';
			echo '<p>Hello World! This is a `' . $f3->VERB . '` verb.<br><br>';
			echo 'Query string posted:';
			echo '<pre>Name = ' . $f3->PARAMS['name'] . '</pre>';
			echo '<pre>Age = ' . $f3->PARAMS['age'] . '</pre>';
			echo '<pre>Profession = ' . $f3->PARAMS['profession'] . '</pre></p>';
			echo '</div>';

			echo '<div id="footer"><h4>This site is powered by <a href="http://fatfree.sourceforge.net">F3</a> - the common sense PHP framework</h4></div>';

			echo '</body></html>';

			return true;
		}
		return false;
	}

	public function main_f3jig_default(Base $f3): bool {
		if ($this->issuccess_init()) {
			$transaction_f3jig = new transaction_f3jig($f3);
			if (isset($transaction_f3jig) && $transaction_f3jig->issuccess_init()) {
				$handle_f3jig = $transaction_f3jig->retrieve_handle();
				if (isset($handle_f3jig)) {
					$f3->main_f3jig_default = array(
						'uuid' => $handle_f3jig->uuid(),
						'dir' => $handle_f3jig->dir()
					);
				}

				$transaction_f3jig->sample_writer();
				$table_data = $transaction_f3jig->sample_reader();
				if (isset($table_data)) {
					$f3->main_f3jig_default += array('sample_table_data' => $table_data);
				}

				$f3->segmentsrender = 'segment_operation_main_f3jig_default.htm';
				echo $this->f3template->render($f3->segmentsdefaultrender);
			}

			return true;
		}
		return false;
	}

	public function main_f3mysql_default(Base $f3): bool {
		if ($this->issuccess_init()) {
			$transaction_f3mysql = new transaction_f3mysql($f3);
			if (isset($transaction_f3mysql) && $transaction_f3mysql->issuccess_init()) {
				$handle_f3mysql = $transaction_f3mysql->retrieve_handle();
				if (isset($handle_f3mysql)) {
					$f3->main_f3mysql_default = array(
						'uuid' => $handle_f3mysql->uuid()
					);
				}

				$transaction_f3mysql->sample_writer();
				$table_data = $transaction_f3mysql->sample_reader();
				if (isset($table_data)) {
					$f3->main_f3mysql_default += array('sample_table_data' => $table_data);
				}

				$f3->segmentsrender = 'segment_operation_main_f3mysql_default.htm';
				echo $this->f3template->render($f3->segmentsdefaultrender);
			}

			return true;
		}
		return false;
	}

	public function main_db_default(Base $f3): bool {
		if ($this->issuccess_init()) {
			$job_db = new job_db($f3, enums\enum_database_type::f3mysql);
			if (isset($job_db) && $job_db->issuccess_init()) {

				$handle_db = $job_db->retrieve_handle();
				if (isset($handle_db)) {
					$f3->main_db_default = array('dbtype' => enums\enum_database_type::f3mysql);
				}

				// Create a specific orm table, with optional specific orm model breadcrumb.
				$job_db->create_table('orm_sample_cortex', '\models\orms');

				// Create all orm tables in a directory, with optional specific orm model breadcrumb.
				$job_db->create_tables('../app/models/orms');

				$result = $job_db->f3mysql_execute('SHOW TABLES');
				if (isset($result)) {
					$f3->main_db_default += array('tables' => $result);
				}

				$f3->segmentsrender = 'segment_operation_main_db_default.htm';
				echo $this->f3template->render($f3->segmentsdefaultrender);
			}

			return true;
		}
		return false;
	}
}