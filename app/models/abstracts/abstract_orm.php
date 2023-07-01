<?php

namespace models\abstracts;

use Exception as Exception;
use \DB\SQL\Schema as Schema;
use \models\abstracts\abstract_model as abstract_model;
use \models\enums\enum_database_type as enum_database_type;
use \models\enums\enum_mysql_datatype as enum_mysql_datatype;
use \jobs\job_db as job_db;
use \jobs\job_exception as job_exception;

abstract class abstract_orm extends abstract_model {
	protected ?job_db $job_db = NULL;
	protected $field_configurations;
	protected $table_name;
	protected $primarykey_name;

	private $datatype_signatures = [
			'BOOLEAN' => 'TINYINT(1)',
			'INT1' => 'TINYINT(4)',
			'INT2' => 'SMALLINT(6)',
			'INT4' => 'INT(11)',
			'INT8' => 'BIGINT(20)',
			'BIGINT' => 'BIGINT(20)',
			'FLOAT' => 'FLOAT',
			'DOUBLE' => 'DECIMAL(18,6)',
			'VARCHAR32' => 'VARCHAR(32)',
			'VARCHAR64' => 'VARCHAR(64)',
			'VARCHAR128' => 'VARCHAR(128)',
			'VARCHAR256' => 'VARCHAR(255)',
			'VARCHAR512' => 'VARCHAR(512)',
			'VARCHAR1024' => 'VARCHAR(1024)',
			'VARCHAR2048' => 'VARCHAR(2048)',
			'VARCHAR4096' => 'VARCHAR(4096)',
			'VARCHAR10240' => 'VARCHAR(10240)',
			'TEXT' => 'TEXT',
			'LONGTEXT' => 'LONGTEXT',
			'DATE' => 'DATE',
			'DATETIME' => 'DATETIME',
			'TIMESTAMP' => 'TIMESTAMP',
			'BLOB' => 'BLOB'
	];
	private $defaulttype_signatures = [
		'CUR_STAMP' => 'CURRENT_TIMESTAMP'
	];

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

		if (isset($this->primarykey_name) && empty($this->primarykey_name)) {
			throw new job_exception('Primarykey name is invalid.');
			return false;
		}

		$this->table_name = '`' . $this->table_name . '`';

		$primarykey_name = isset($this->primarykey_name) && !empty($this->primarykey_name) ? $this->primarykey_name : 'id';
		$field_configuration_primarykey = [
			'type' => enum_mysql_datatype::BIGINT,
			'nullable' => false,
			'autoincrement' => true
		];
		$this->field_configurations = array_merge([$primarykey_name => $field_configuration_primarykey], $this->field_configurations);

		return true;
	}

	public function create_table() {
		try {
			$column_strings = [];
			foreach ($this->field_configurations as $field_name => $field_configuration) {
				$fieldname = '`' . $field_name . '`';

				$type = isset($field_configuration['type']) ? $this->datatype_signatures[$field_configuration['type']] : false;

				$nullable = '';
				if (isset($field_configuration['nullable']) && $field_configuration['nullable'] === false) {
					$nullable = 'NOT NULL';
				}
				else {
					$nullable = 'NULL';
				}

				$autoincrement = '';
				if (isset($field_configuration['autoincrement']) && $field_configuration['autoincrement'] === true) {
					$autoincrement = 'AUTO_INCREMENT';
				}

				$default = '';
				if (isset($field_configuration['default'])) {
					if (isset($this->defaulttype_signatures[$field_configuration['default']])) {
						$default = 'DEFAULT ' . $this->defaulttype_signatures[$field_configuration['default']];
					}
					else {
						$default = 'DEFAULT \'' . $field_configuration['default'] . '\'';
					}
				}

				array_push($column_strings, [
					'fieldname' => $fieldname,
					'type' => $type,
					'nullable' => $nullable,
					'autoincrement' => $autoincrement,
					'default' => $default
				]);
			}

			echo '<table><tr>';
			echo '<caption>Table Name: ' . $this->table_name . '</caption>';
			echo '<th>Field Name</th>
			<th>Type</th>
			<th>IS NULL</th>
			<th>Autoincrement</th>
			<th>Default Value</th></tr>';
			foreach ($column_strings as $column_string) {
				echo '<tr>';

				echo '<td>' . $column_string['fieldname'] . '</td>';
				echo '<td>' . $column_string['type'] . '</td>';
				echo '<td>' . $column_string['nullable'] . '</td>';
				echo '<td>' . $column_string['autoincrement'] . '</td>';
				echo '<td>' . $column_string['default'] . '</td>';

				echo '</tr>';
			}
			echo '</table>';

			// print_r($this->field_configurations);

			// $result = $this->job_db->f3mysql_execute('SHOW TABLES');
			// if (isset($result)) {
			// 	print_r($result);
			// }
		}
		catch (Exception $exception) {
			throw new job_exception('Table \'' . $this->table_name . '\' couldn\'t be created.', $exception);
		}
	}
}
