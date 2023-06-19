<?php

namespace jobs;

use Exception;
use \Base as Base;
use \jobs\job_exception as job_exception;

class job_db {
	private ?object $handle_this = NULL;
	private Base $config_f3;
	private string $config_database_type = '';

	public function __construct(Base $f3, string $database_type) {
		$this->config_f3 = $f3;
		$this->config_database_type = $database_type;

		if ($this->validate_config()) {
			return $this->handshake();
		}
		return false;
	}

	private function validate_config(): bool {
		if (isset($this->config_f3)) {
			if (isset($this->config_database_type) && !empty($this->config_database_type)) {
				return true;
			}
			else {
				throw new job_exception("Database type is invalid.");
			}
		}
		else {
			throw new job_exception("F3 instance is null.");
		}
		return false;
	}

	private function handshake(): bool {
		try {
			// TODO: put 'initialization' logics here
			$this->handle_this = 'to be replaced with real basic object';

			return true;
		}
		catch (Exception $exception) {
			$this->handle_this = NULL;
			throw new job_exception("Database unable to initialized.", $exception);
			return false;
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

	public function retrieve_handle(): ?object {
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
