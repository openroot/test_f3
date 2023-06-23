<?php

namespace jobs;

use Exception;
use \Base as Base;
use \jobs\job_exception as job_exception;
use \transactions\transaction_f3jig as transaction_f3jig;
use \transactions\transaction_f3mysql as transaction_f3mysql;
use models\enum_database_type;

class job_db {
	private ?bool $handle_this = NULL;
	private Base $config_f3;
	private string $config_enum_database_type = '';
	private ?string $database_id = '';

	public function __construct(Base $f3, string $enum_database_type, ?string $database_id = NULL) {
		$this->config_f3 = $f3;
		$this->config_enum_database_type = $enum_database_type;
		$this->database_id = $database_id;

		if ($this->validate_config()) {
			return $this->handshake();
		}
		return false;
	}

	private function validate_config(): bool {
		if (isset($this->config_f3)) {
			if (isset($this->config_enum_database_type)
				// TODO: Add enum existance check.
			) {
				return true;
			}
			else {
				throw new job_exception('Database type is invalid.');
			}
		}
		else {
			throw new job_exception('F3 instance is null.');
		}
		return false;
	}

	private function handshake(): bool {
		$this->database_id = isset($this->database_id) && !empty($this->database_id) ? $this->database_id : $this->config_f3->get('job.db.default.id');
		try {
			switch ($this->config_enum_database_type) {
				case enum_database_type::f3mysql:
					$handle_f3msql = (new transaction_f3mysql($this->config_f3))->retrieve_handle();
					$this->config_f3->set($this->database_id, $handle_f3msql);
					break;

				case enum_database_type::f3jig:
					$handle_f3jig = (new transaction_f3jig($this->config_f3))->retrieve_handle();
					$this->config_f3->set($this->database_id, $handle_f3jig);
					break;

				default:
					throw new job_exception('Database type is not valid.');
					break;
			}

			$this->handle_this = true;

			return true;
		}
		catch (Exception $exception) {
			$this->destroy_handle();
			throw new job_exception('Database unable to initialized.', $exception);
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

	public function retrieve_handle(): ?bool {
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

	public function create_table($orm_model): bool {
		if ($this->issuccess_init()) {
			try {
				$orm_model::setup();
				return true;
			}
			catch (Exception $exception) {
				throw new job_exception('Table Couldn\'t created.', $exception);
			}
		}
		return false;
	}
}
