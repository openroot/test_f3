<?php
namespace jobs;
use Exception;

class job_exception extends Exception {
	private string $config_message = '';
	private ?Exception $config_exception = NULL;
	private ?string $handle_full_message = NULL;

	public function __construct(string $message, Exception $exception = NULL) {
		$this->config_message = $message;
		$this->config_exception = $exception;

		if ($this->validate_config()) {
			return $this->handshake();
		}
		return false;
	}

	private function validate_config(): bool {
		if (isset($this->config_message) && !empty($this->config_message)) {
			return true;
		}
		else {
			throw new Exception("Exception message is invalid.");
		}
		return false;
	}

	private function handshake(): bool {
		if (isset($this->config_exception)) {
			$this->handle_full_message = 'App Exception: ' . $this->config_message . ' [ ' . $this->config_exception->message . ' ]';
		}
		else {
			$this->handle_full_message = 'App Exception: ' . $this->config_message;
		}
		// TODO: default it to non-binary-file logged
		throw new Exception($this->handle_full_message);
		return true;
	}

	public function issuccess_init(): bool {
		if (isset($this->handle_full_message)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function retrieve_handle(): ?string {
		if (isset($this->handle_full_message)) {
			return $this->handle_full_message;
		}
		else {
			if ($this->validate_config() && $this->handshake()) {
				return $this->handle_full_message;
			}
		}
		return NULL;
	}
}