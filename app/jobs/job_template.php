<?php

namespace jobs;

use Exception as Exception;
use \jobs\job_exception as job_exception;

class job_template {
	private ?object $handle_this = NULL;
	private string $config_1 = '';
	private string $config_2 = '';

	public function __construct(string $config_1, string $config_2) {
		$this->config_1 = $config_1;
		$this->config_2 = $config_2;

		if ($this->validate_config()) {
			return $this->handshake();
		}
		return false;
	}

	// (todo): This would  be a class specification.
	private function validate_config(): bool {
		if (isset($this->config_1) && !empty($this->config_1)) {
			if (isset($this->config_2) && !empty($this->config_2)) {
				return true;
			}
			else {
				throw new job_exception("Config 2 is invalid.");
			}
		}
		else {
			throw new job_exception("Config 1 is invalid.");
		}
		return false;
	}

	// (todo): This would  be a class specification.
	private function handshake(): bool {
		try {
			// (todo): Put 'initialization' logics here.
			$this->handle_this = 'To be replaced with real basic object';

			return true;
		}
		catch (Exception $exception) {
			$this->destroy_handle(); // (todo): Keep this destroyer only when this method concerned is crucial to operational completion.
			throw new job_exception("This-job unable to initialized.", $exception);
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
