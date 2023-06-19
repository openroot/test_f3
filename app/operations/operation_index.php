<?php

namespace operations;

use Exception as Exception;
use \Base as Base;
use \Template as Template;
use \transactions\transaction_f3jig as transaction_f3jig;
use \jobs\job_exception as job_exception;

class operation_index {
	private ?string $handle_this = NULL;
	private ?object $config_f3templateinstance = NULL;

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
				return true;
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
				$this->config_f3templateinstance = Template::instance();

				return true;
			}
			catch (Exception $exception) {
				$this->destroy_handle();
				throw new job_exception('F3 singletones couldn\'t be initialized.', $exception);
			}
		}
		return false;
	}

	public function helloworld_default(Base $f3): bool {
		if ($this->issuccess_init()) {
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

			return true;
		}
		return false;
	}

	public function f3jig_default(Base $f3): bool {
		if ($this->issuccess_init()) {
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
				echo $this->config_f3templateinstance->render($f3->segmentappdefault);
			}
			
			return true;
		}
		return false;
	}
}