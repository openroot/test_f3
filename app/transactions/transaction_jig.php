<?php
namespace transactions;
use Exception;
use \Base as Base;
use \DB\Jig as Jig;
use \jobs\job_exception as job_exception;

class transaction_jig {
	private Base $config_f3;
	private string $config_f3jig_database_name = '';
	private ?Jig $handle_f3jig = NULL;
	
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
			$this->handle_f3jig = new Jig($this->config_f3->blobf3jigpath . $this->config_f3jig_database_name, Jig::FORMAT_JSON);
			return true;
		}
		catch (Exception $exception) {
			$this->handle_f3jig = NULL;
			throw new job_exception("F3-Jig plug-in unable to initialized.", $exception);
			return false;
		}
		return false;
	}

	public function issuccess_init(): bool {
		if (isset($this->handle_f3jig)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function retrieve_handle(): ?Jig {
		if (isset($this->handle_f3jig)) {
			return $this->handle_f3jig;
		}
		else {
			if ($this->validate_config() && $this->handshake()) {
				return $this->handle_f3jig;
			}
		}
		return NULL;
	}

	public function simple_writer(string $table_name): void {
		if (isset($table_name) && !empty($table_name)) {
			$mapper = new Jig\Mapper($this->handle_f3jig, $table_name);
			$mapper->username = 'userA';
			$mapper->password = '57d82jg05';
			$mapper->save();
			$mapper->reset();
			$mapper->username = 'userB';
			$mapper->password = 'kbjd94973';
			$mapper->save();
		}
	}

	public function simple_reader(string $table_name): ?array {
		$data = array();
		if (isset($table_name) && !empty($table_name)) {
			$data = $this->handle_f3jig->read($table_name);
		}
		return $data;
	}
}