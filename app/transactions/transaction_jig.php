<?php

namespace transactions;

use Exception;
use \Base as Base;
use \DB\Jig as Jig;
use \jobs\job_exception as job_exception;

class transaction_jig {
	private ?Jig $handle_this = NULL;
	private Base $config_f3;
	private string $config_f3jig_database_name = '';
	
	public function __construct(Base $f3, string $f3jig_database_name) {
		$this->config_f3 = $f3;
		$this->config_f3jig_database_name = $f3jig_database_name;

		if ($this->validate_config()) {
			return $this->handshake();
		}
		return false;
	}

	private function validate_config(): bool {
		if (isset($this->config_f3)) {
			if (isset($this->config_f3jig_database_name) && !empty($this->config_f3jig_database_name)) {
				return true;
			}
			else {
				throw new job_exception("F3-Jig database name is invalid.");
			}
		}
		else {
			throw new job_exception("F3 instance is null.");
		}
		return false;
	}

	private function handshake(): bool {
		try {
			$this->handle_this = new Jig($this->config_f3->blobf3jigpath . $this->config_f3jig_database_name, Jig::FORMAT_JSON);

			return true;
		}
		catch (Exception $exception) {
			$this->destroy_handle();
			throw new job_exception("F3-Jig plug-in unable to initialized.", $exception);
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

	public function retrieve_handle(): ?Jig {
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

	public function simple_writer(string $table_name): bool {
		if ($this->issuccess_init()) {
			try {
				if (isset($table_name) && !empty($table_name)) {
					$mapper = new Jig\Mapper($this->handle_this, $table_name);
					
					$mapper->username = 'userA';
					$mapper->password = '57d82jg05';
					$mapper->save();
					$mapper->reset();
					$mapper->username = 'userB';
					$mapper->password = 'kbjd94973';
					$mapper->save();

					return true;
				}
			}
			catch (Exception $exception) {
				throw new job_exception("Unable to write data.", $exception);
			}
		}
		return false;
	}

	public function simple_reader(string $table_name): ?array {
		if ($this->issuccess_init()) {
			try {
				if (isset($table_name) && !empty($table_name)) {
					return $this->handle_this->read($table_name);
				}
			}
			catch (Exception $exception) {
				throw new job_exception("Unable to read data.", $exception);
			}
		}
		return NULL;
	}
}