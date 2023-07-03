<?php

namespace transactions;

use Exception;
use \Base as Base;
use \DB\SQL as SQL;
use \jobs\job_exception as job_exception;

class transaction_f3mysql {
	private ?SQL $handle_this = null;

	private Base $config_f3;
	private ?string $config_databasename = null;

	public function __construct(Base $f3, ?string $databasename = null) {
		$this->config_f3 = $f3;
		$this->config_databasename = $databasename;

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

		$this->config_databasename = (isset($this->config_databasename) && !empty($this->config_databasename))
			? $this->config_databasename
			: $this->config_f3->get('transactions.f3mysql.databasename');

		if (!(isset($this->config_databasename) && !empty($this->config_databasename))) {
			throw new job_exception('MySQL database name is invalid.');
			return false;
		}

		return true;
	}

	private function handshake(): bool {
		try {
			$this->handle_this = new SQL('mysql:host=localhost;
				port=' . $this->config_f3->get('transactions.f3mysql.port') . ';
				dbname=' . $this->config_f3->get('transactions.f3mysql.databasename'),
				$this->config_f3->get('transactions.f3mysql.username'),
				$this->config_f3->get('transactions.f3mysql.password'));
		}
		catch (Exception $exception) {
			$this->destroy_handle();
			throw new job_exception('F3-SQL plug-in unable to initialized.', $exception);
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

	public function retrieve_handle(): ?SQL {
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

	public function f3mappedtable(string $tablename): SQL\Mapper {
		if ($this->issuccess_init()) {
			if (!empty($tablename)) {
				try {
					$mapper = new SQL\Mapper($this->handle_this, $tablename);
					return $mapper;
				}
				catch (Exception $exception) {
					throw new job_exception('Unable to create f3mapped table.', $exception);
				}
			}
		}
		return null;
	}

	public function demo_insert(?string $tablename = null): bool {
		if ($this->issuccess_init()) {
			$tablename = (isset($tablename) && !empty($tablename)) ? $tablename : 'sample_table';
			try {
				if (isset($tablename) && !empty($tablename)) {
					$sample_table = $this->f3mappedtable($tablename); // It expects a pre-qualified database 'test_f3' and table 'sample_table' in mysql.

					$sample_table->username = 'userA';
					$sample_table->password = '57d82jg05';
					$sample_table->save();
					$sample_table->reset();
					$sample_table->username = 'userB';
					$sample_table->password = 'kbjd94973';
					$sample_table->save();
				}
			}
			catch (Exception $exception) {
				throw new job_exception('Unable to insert data.', $exception);
				return false;
			}
		}
		return true;
	}

	public function demo_select(?string $tablename = null): ?array {
		if ($this->issuccess_init()) {
			$tablename = (isset($tablename) && !empty($tablename)) ? $tablename : 'sample_table';
			try {
				if (isset($tablename) && !empty($tablename)) {
					return $this->f3mappedtable($tablename)->find(''); // It expects a pre-qualified database 'test_f3' and table 'sample_table' in mysql.
				}
			}
			catch (Exception $exception) {
				throw new job_exception('Unable to select data.', $exception);
			}
		}
		return null;
	}
}
