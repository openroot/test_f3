<?php

namespace models\abstracts;

use Exception as Exception;
use \models\abstracts\abstract_model as abstract_model;
use \models\enums\enum_database_type as enum_database_type;
use \jobs\job_db as job_db;
use \jobs\job_exception as job_exception;

abstract class abstract_orm extends abstract_model {
	protected ?job_db $job_db = NULL;
	protected $field_configurations;
	protected $table_name;
	protected $primarykeyfield_name;

	public function __construct() {
		if (!isset($this->f3)) {
			parent::__construct();
		}
		if (isset($this->f3) && !isset($this->job_db)) {
			try {
				$this->job_db = new job_db($this->f3, enum_database_type::f3mysql);
			}
			catch (Exception $exception) {
				throw new job_exception('Job db couldn\'t be instaniated.', $exception);
				return false;
			}
		}
		return $this->validate_config();
	}

	private function validate_config(): bool {
		if (!(isset($this->field_configurations) && !empty($this->field_configurations))) {
			throw new job_exception('Field configurations are invalid.');
			return false;
		}
		if (!(isset($this->table_name) && !empty($this->table_name))) {
			throw new job_exception('Table name is invalid.');
			return false;
		}
		if (!(isset($this->primarykeyfield_name) && !empty($this->primarykeyfield_name))) {
			throw new job_exception('Primarykey field name is invalid.');
			return false;
		}
		return true;
	}

	public function create_table() {
		try {
			print_r($this->field_configurations);

			$result = $this->job_db->f3mysql_execute('SHOW TABLES');
			if (isset($result)) {
				print_r($result);
			}
		}
		catch (Exception $exception) {
			throw new job_exception('Table \'' . $this->table_name . '\' couldn\'t be created.', $exception);
		}
	}
}
