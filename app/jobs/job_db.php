<?php

namespace jobs;

use Exception;
use \Base as Base;
use \jobs\job_exception as job_exception;
use \transactions\transaction_f3jig as transaction_f3jig;
use \transactions\transaction_f3mysql as transaction_f3mysql;
use \models\enums as enums;

class job_db {
	private mixed $handle_this = null;

	private Base $config_f3;
	private string $config_enum_database_type = '';
	private ?string $config_database_id = null;

	public function __construct(Base $f3, string $enum_database_type, ?string $database_id = null) {
		$this->config_f3 = $f3;
		$this->config_enum_database_type = $enum_database_type;
		$this->config_database_id = $database_id;

		if ($this->validate_config()) {
			return $this->handshake();
		}
		return false;
	}

	private function validate_config(): bool {
		if (!isset($this->config_f3)) {
			throw new job_exception('F3 instance is null.');
			return false;
		}
		if (!isset($this->config_enum_database_type)) { // TODO: Check enum existance.
			throw new job_exception('Database type is invalid.');
			return false;
		}

		$this->config_database_id = isset($this->config_database_id) && !empty($this->config_database_id)
			? $this->config_database_id
			: $this->config_f3->get('job.db.default.id');

		if (!(isset($this->config_database_id) && !empty($this->config_database_id))) {
			throw new job_exception('Database ID is invalid.');
			return false;
		}

		return true;
	}

	private function handshake(): bool {
		try {
			switch ($this->config_enum_database_type) {
				case enums\enum_database_type::f3mysql:
					$this->handle_this = (new transaction_f3mysql($this->config_f3))->retrieve_handle();
					$this->config_f3->set($this->config_database_id, $this->handle_this);
					break;

				case enums\enum_database_type::f3jig:
					$this->handle_this = (new transaction_f3jig($this->config_f3))->retrieve_handle();
					$this->config_f3->set($this->config_database_id, $this->handle_this);
					break;

				default:
					throw new job_exception('Database type is invalid.');
					break;
			}
		}
		catch (Exception $exception) {
			$this->destroy_handle();
			throw new job_exception('Database unable to initialized.', $exception);
			return false;
		}
		return true;
	}

	public function issuccess_init(): bool {
		if (!isset($this->handle_this)) {
			return false;
		}
		return true;
	}

	public function retrieve_handle(): mixed {
		if (isset($this->handle_this)) {
			return $this->handle_this;
		}
		else {
			if ($this->validate_config() && $this->handshake()) {
				return $this->handle_this;
			}
		}
		return null;
	}

	public function destroy_handle() {
		$this->handle_this = null;
	}

	public function mysqlexec(string $mysqlstatement) {
		if ($this->issuccess_init()) {
			if (isset($mysqlstatement) && !empty($mysqlstatement)) {
				try {
					return $this->handle_this->exec($mysqlstatement);
				}
				catch (Exception $exception) {
					throw new job_exception("MySQL were unable to execute.", $exception);
				}
			}
		}
		return null;
	}
}
