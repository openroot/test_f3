<?php

namespace jobs;

use \Base as Base;
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

				$successchain = false;

				$directory = '../app/models/orms';
				$modelsorms_breadcrumb = '\models\orms';
				
				$modelorm_names = array();
				$modelorm_names_temp = $job_db->get_modelsorms_names($directory, $modelsorms_breadcrumb);
				foreach ($modelorm_names_temp as $modelorm_name_temp) {
					array_push($modelorm_names, str_replace('orm_', '', $modelorm_name_temp));
				}

				// Create modelorm tables.
				$this->prepare_mysql_add_tables($job_db, $modelorm_names, $directory);

				// Create modelorm foreignkeys.
				$successchain = $this->prepare_mysql_add_foreignkeys($job_db);

				if ($successchain) {
					// TODO: Put database-seeding script here.
				}

				return true;

			}
		 }
		 return false;
	}

	public function prepare_mysql_add_tables(job_db $job_db, array $modelorm_names, string $directory): bool {
		if ($this->issuccess_init()) {
			if (isset($job_db) && isset($modelorm_names) && isset($directory)) {

				// Fetch tables exists in database.
				$tableindatabase_names = array();
				$result = $job_db->f3mysql_execute('SHOW TABLES');
				if (isset($result)) {
					foreach ($result as $table) {
						array_push($tableindatabase_names, $table['Tables_in_test_f3']);
					}
				}

				// Check if any modelorms are missing in database.
				$successchain = true;
				foreach ($modelorm_names as $modelorm_name) {
					$successchain = array_search($modelorm_name, $tableindatabase_names) !== false ? $successchain : false;
				}

				// Create tables (only if any missing).
				if (!$successchain) {
					if ($job_db->create_tables($directory)) {
						return true;
					}
				}

			}
		}
		return false;
	}

	public function prepare_mysql_add_foreignkeys(job_db $job_db): bool {
		if ($this->issuccess_init()) {
			if (isset($job_db)) {

				// TODO: Create foreign_key adding script here.

				$statement = 'ALTER TABLE `order`
					ADD CONSTRAINT `order_product_id`  
					FOREIGN KEY ( `product_id` ) REFERENCES `product` ( `id` )
					ON DELETE CASCADE ON UPDATE RESTRICT';

				//$result = $job_db->f3mysql_execute($statement);

				return true;

			}
		}
		return false;
	}
}