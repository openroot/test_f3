<?php

namespace models\abstracts;

use Exception as Exception;
use \models\abstracts\abstract_model as abstract_model;
use \models\enums as enums;
use \jobs\job_db as job_db;
use \jobs\job_exception as job_exception;

abstract class abstract_orm extends abstract_model {
	private $mysqlfield_type_signatures = [
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
	private $mysqlfield_attributes_signatures = [
		'UNSIGNED' => 'UNSIGNED',
		'ON_UPDATE_CURRENT_TIMESTAMP' => 'on update CURRENT_TIMESTAMP'
	];
	private $mysqlfield_default_signatures = [
		'CURRENT_TIMESTAMP' => 'CURRENT_TIMESTAMP'
	];

	protected ?job_db $job_db = NULL;
	protected $fieldconfigs;
	protected $tablename;
	protected $primarykeyname;

	public function __construct() {
		if (!isset($this->f3)) {
			parent::__construct();
		}
		if (isset($this->f3) && !isset($this->job_db)) {
			try {
				$this->job_db = new job_db($this->f3, enums\enum_database_type::f3mysql);
			}
			catch (Exception $exception) {
				throw new job_exception('Job db couldn\'t be instaniated.', $exception);
				return false;
			}
		}
		if ($this->validate_config()) {
			$this->add_meta_fieldconfigs();
		}
		else {
			return false;
		}
		return true;
	}

	private function validate_config(): bool {
		if (!(isset($this->fieldconfigs) && !empty($this->fieldconfigs))) {
			throw new job_exception('Field configurations are invalid.');
			return false;
		}

		if (!(isset($this->tablename) && !empty($this->tablename))) {
			throw new job_exception('Table name is invalid.');
			return false;
		}

		if (isset($this->primarykeyname) && empty($this->primarykeyname)) {
			throw new job_exception('Primarykey name is invalid.');
			return false;
		}

		return true;
	}

	private function add_meta_fieldconfigs() {
		$this->tablename = '`' . $this->tablename . '`';

		$fieldconfigs = [
			isset($this->primarykeyname) && !empty($this->primarykeyname) ? $this->primarykeyname : 'id' => [
				enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::BIGINT,
				enums\enum_orm_fieldconfigparam::nullable => false,
				enums\enum_orm_fieldconfigparam::autoincrement => true,
				enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::PRIMARYKEY
			]
		];

		$this->fieldconfigs = array_merge($fieldconfigs, $this->fieldconfigs);
	}

	public function create_table() {
		try {
			$column_strings = [];
			foreach ($this->fieldconfigs as $field_name => $fieldconfig) {
				$fieldname = isset($field_name) && !empty($field_name) ? '`' . $field_name . '`' : false;
				$type = isset($fieldconfig[enums\enum_orm_fieldconfigparam::type]) && isset($this->mysqlfield_type_signatures[$fieldconfig[enums\enum_orm_fieldconfigparam::type]])
					? $this->mysqlfield_type_signatures[$fieldconfig[enums\enum_orm_fieldconfigparam::type]]
					: false;

				if ($type) {
					$nullable = '';
					if (isset($fieldconfig['nullable']) && $fieldconfig['nullable'] === false) {
						$nullable = 'NOT NULL';
					}
					else {
						$nullable = 'NULL';
					}

					$autoincrement = '';
					if (isset($fieldconfig['autoincrement']) && $fieldconfig['autoincrement'] === true) {
						$autoincrement = 'AUTO_INCREMENT';
					}

					$default = '';
					if (isset($fieldconfig['default'])) {
						if (isset($this->mysqlfield_default_signatures[$fieldconfig['default']])) {
							$default = 'DEFAULT ' . $this->mysqlfield_default_signatures[$fieldconfig['default']];
						}
						else {
							$default = 'DEFAULT \'' . $fieldconfig['default'] . '\'';
						}
					}

					array_push($column_strings, [
						'fieldname' => $fieldname,
						enums\enum_orm_fieldconfigparam::type => $type,
						'nullable' => $nullable,
						'autoincrement' => $autoincrement,
						'default' => $default
					]);
				}
			}

			echo '<table><tr>';
			echo '<caption>Table Name: ' . $this->tablename . '</caption>';
			echo '<th>Field Name</th>
			<th>Type</th>
			<th>IS NULL</th>
			<th>Autoincrement</th>
			<th>Default Value</th></tr>';
			foreach ($column_strings as $column_string) {
				echo '<tr>';

				echo '<td>' . $column_string['fieldname'] . '</td>';
				echo '<td>' . $column_string[enums\enum_orm_fieldconfigparam::type] . '</td>';
				echo '<td>' . $column_string['nullable'] . '</td>';
				echo '<td>' . $column_string['autoincrement'] . '</td>';
				echo '<td>' . $column_string['default'] . '</td>';

				echo '</tr>';
			}
			echo '</table>';

			// $result = $this->job_db->f3mysql_execute('SHOW TABLES');
			// if (isset($result)) {
			// 	print_r($result);
			// }
		}
		catch (Exception $exception) {
			throw new job_exception('Table \'' . $this->tablename . '\' couldn\'t be created.', $exception);
		}
	}
}
