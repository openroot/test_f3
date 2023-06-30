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

				$config_foreignkeys = [[
					'source_table' => 'order',
					'target_table' => 'product',
					'source_table_id' => 'fk_product_id',
					'target_table_id' => 'id',
					'ondelete' => 'CASCADE',
					'onupdate' => 'RESTRICT'
				],[
					'source_table' => 'order',
					'target_table' => 'customer',
					'source_table_id' => 'fk_customer_id',
					'target_table_id' => 'id',
					'ondelete' => 'CASCADE',
					'onupdate' => 'RESTRICT'
				]];

				foreach ($config_foreignkeys as $config_foreignkey) {
					$source_table = $config_foreignkey['source_table'];
					$target_table = $config_foreignkey['target_table'];
					$source_table_id = $config_foreignkey['source_table_id'];
					$target_table_id = $config_foreignkey['target_table_id'];
					$ondelete = $config_foreignkey['ondelete'];
					$onupdate = $config_foreignkey['onupdate'];
					$constraint_name = 'fk_' . $source_table . '_' . $target_table . '_' . $target_table_id;

					// TODO: Check constraint already exists.

					$mysql_statement = 'ALTER TABLE `' . $source_table . '`
					ADD CONSTRAINT `' . $constraint_name . '`  
					FOREIGN KEY ( `' . $source_table_id . '` ) REFERENCES `' . $target_table . '` ( `' . $target_table_id . '` )
					ON DELETE ' . $ondelete . ' ON UPDATE ' . $onupdate;

					//$result = $job_db->f3mysql_execute($mysql_statement);
				}

				return true;

			}
		}
		return false;
	}
}