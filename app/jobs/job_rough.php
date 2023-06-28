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

	public function prepare_mysql(job_db $job_db): bool {
		 if ($this->issuccess_init()) {
			if (isset($job_db)) {

				$directory = '../app/models/orms';
				$modelsorms_breadcrumb = '\models\orms';
				
				// Check if modelorm tables already exists in database
				$modelorm_names_temp = $job_db->get_modelsorms_names($directory, $modelsorms_breadcrumb);
				$modelorm_names = array();
				foreach ($modelorm_names_temp as $modelorm_name_temp) {
					array_push($modelorm_names, str_replace('orm_', '', $modelorm_name_temp));
				}
				
				$result = $job_db->f3mysql_execute('SHOW TABLES');
				if (isset($result)) {
					$table_names = array();
					foreach ($result as $table) {
						array_push($table_names, $table['Tables_in_test_f3']);
					}

					$successchain = true;
					foreach ($modelorm_names as $modelorm_name) {
						$successchain = array_search($modelorm_name, $table_names) !== false ? $successchain : false;
					}

					// Create tables (only if one or many missing)
					if (!$successchain) {
						if ($job_db->create_tables($directory)) {
							// TODO: Create foreign_key adding script here.

							return true;
						}
					}
					else {
						return true;
					}
				}

			}
		 }
		 return false;
	}
}