<?php

namespace jobs;

use Exception as Exception;
use \Base as Base;
use \jobs\job_exception as job_exception;
use \jobs\job_db as job_db;

class job_rough {
	private Base $config_f3;

	public function __construct(Base $f3) {
		if (isset($f3)) {
			$this->config_f3 = $f3;
		}
	}

	public function issuccess_init(): bool {
		if (isset($this->config_f3)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function prepare_mysql(job_db $job_db) {
		 if ($this->issuccess_init()) {
			if (isset($job_db)) {
				$job_db->create_tables('../app/models/orms');

				$result = $job_db->f3mysql_execute('SHOW TABLES');
				if (isset($result)) {
					$this->config_f3->index_db_default += array('tables' => $result);
				}
			}
		 }
	}
}