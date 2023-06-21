<?php

namespace jobs;

use Exception;
use \Base as Base;
use \jobs\job_exception as job_exception;
use \models as models;
use \transactions\transaction_f3jig as transaction_f3jig;
use \DB\Cortex as Cortex;

class job_db {
	private ?string $handle_this = NULL;
	private Base $config_f3;
	private string $config_enum_database_type = '';

	public function __construct(Base $f3, string $enum_database_type) {
		$this->config_f3 = $f3;
		$this->config_enum_database_type = $enum_database_type;

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
		try {
			// TODO: Put 'initialization' logics here.
			$db = new transaction_f3jig($this->config_f3);
			$data = $db->sample_reader('users.json');

			$this->handle_this = 'to be replaced with real basic object';

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
}
