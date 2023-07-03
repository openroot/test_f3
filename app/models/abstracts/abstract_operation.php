<?php

namespace models\abstracts;

use Exception as Exception;
use \Template as Template;
use \jobs\job_exception as job_exception;

use \jobs\job_db as job_db;
use \models\enums as enums;

abstract class abstract_operation extends abstract_model {
	private ?string $handle_this = null;

	protected Template $f3template;
	protected job_db $job_db;

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
			return false;
		}
		try {
			$this->f3template = Template::instance();
		}
		catch (Exception $exception) {
			throw new job_exception('F3 Template instance couldn\'t be initialized.', $exception);
			return false;
		}
		try {
			$this->job_db = new job_db($this->f3, enums\enum_database_type::f3mysql);
		}
		catch (Exception $exception) {
			throw new job_exception('Database job couldn\'t be initialized.', $exception);
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

	public function retrieve_handle(): ?string {
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
}