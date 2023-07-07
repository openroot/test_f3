<?php

namespace models\abstracts;

use Exception as Exception;
use \models\abstracts\abstract_model as abstract_model;
use \models\enums as enums;
use \jobs\job_db as job_db;
use \jobs\job_exception as job_exception;
use \jobs\job_rough as job_rough;
use \models\extensions\mapper as mapper;

abstract class abstract_orm extends abstract_model {
	private $mysqlfield_type_signatures = [
			'BOOLEAN' => 'TINYINT(1)',
			'INT1' => 'TINYINT(4)',
			'INT2' => 'SMALLINT(6)',
			'INT4' => 'INT(11)',
			'INT8' => 'BIGINT(20)',
			'BIGINT' => 'BIGINT(20)',
			'FLOAT' => 'FLOAT',
			'DOUBLE' => 'DECIMAL(20,6)',
			'VARCHAR32' => 'VARCHAR(32)',
			'VARCHAR64' => 'VARCHAR(64)',
			'VARCHAR128' => 'VARCHAR(128)',
			'VARCHAR256' => 'VARCHAR(256)',
			'VARCHAR512' => 'VARCHAR(512)',
			'VARCHAR1024' => 'VARCHAR(1024)',
			'VARCHAR2048' => 'VARCHAR(2048)',
			'VARCHAR4096' => 'VARCHAR(4096)',
			'VARCHAR10240' => 'VARCHAR(10240)',
			'TINYTEXT' => 'TINYTEXT',
			'TEXT' => 'TEXT',
			'LONGTEXT' => 'LONGTEXT',
			'DATE' => 'DATE',
			'DATETIME' => 'DATETIME',
			'TIMESTAMP' => 'TIMESTAMP',
			'BLOB' => 'BLOB',
			'JSON' => 'JSON'
	];
	private $mysqlfield_attributes_signatures = [
		'UNSIGNED' => 'UNSIGNED',
		'ON_UPDATE_CURRENT_TIMESTAMP' => 'on update CURRENT_TIMESTAMP'
	];
	private $mysqlfield_default_signatures = [
		'CURRENT_TIMESTAMP' => 'CURRENT_TIMESTAMP'
	];
	private $mysqlfield_index_signatures = [
		'PRIMARYKEY' => 'PRIMARY KEY',
		'INDEX' => 'INDEX',
		'UNIQUE' => 'UNIQUE',
	];

	protected $fieldconfigs;
	protected $fkconfigs;

	protected ?job_db $job_db = null;
	private $tablename;
	private array $fktablenames = [];

	public function __construct() {
		if (strstr(get_parent_class($this), 'abstract_orm')) {
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
				$this->add_metafieldconfigs();
			}
			else {
				throw new job_exception('Table configurations are invalid.');
				return false;
			}
		}
		else {
			throw new job_exception('It is not an orm class.');
			return false;
		}
		return true;
	}

	private function validate_config(): bool {
		if (!(isset($this->fieldconfigs) && !empty($this->fieldconfigs))) {
			throw new job_exception('Field configurations are invalid.');
			return false;
		}

		$this->tablename = job_rough::extract_tablename_from_ormclassname(get_class($this));
		if (!isset($this->tablename)) {
			throw new job_exception('Table name is invalid, must have been preceded with \'orm_\'.');
			return false;
		}

		if (isset($this->fkconfigs) && is_array($this->fkconfigs) && (count($this->fkconfigs) > 0)) {
			foreach ($this->fkconfigs as $fkconfig) {
				$parenttablename = job_rough::extract_tablename_from_ormclassname($fkconfig);
				if (!isset($parenttablename)) {
					throw new job_exception('Parent table name for foreign key is invalid, must have been preceded with \'orm_\'.');
				}
				else {
					$this->fktablenames += ['fk_' . $parenttablename . '_meta_id' => $parenttablename];
				}
			}
		}

		return true;
	}

	private function add_metafieldconfigs() {
		$fieldconfigs = [
			'meta_id' => [
				enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::BIGINT,
				enums\enum_orm_fieldconfigparam::attributes => enums\enum_mysqlfield_attributes::UNSIGNED,
				enums\enum_orm_fieldconfigparam::nullable => false,
				enums\enum_orm_fieldconfigparam::autoincrement => true,
				enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::PRIMARYKEY,
				enums\enum_orm_fieldconfigparam::comment => '`' . $this->get_tablename() . '` primary key'
			],
			'meta_created_at' => [
				enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::TIMESTAMP,
				enums\enum_orm_fieldconfigparam::nullable => false,
				enums\enum_orm_fieldconfigparam::default => enums\enum_mysqlfield_default::CURRENT_TIMESTAMP,
				enums\enum_orm_fieldconfigparam::comment => 'Creation timestamp'
			],
			'meta_updated_at' => [
				enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::TIMESTAMP,
				enums\enum_orm_fieldconfigparam::attributes => enums\enum_mysqlfield_attributes::ON_UPDATE_CURRENT_TIMESTAMP,
				enums\enum_orm_fieldconfigparam::comment => 'Updation timestamp'
			],
			'meta_z_order' => [
				enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::BIGINT,
				enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::UNIQUE,
				enums\enum_orm_fieldconfigparam::comment => 'Rational aspect'
			],
			'meta_authentication' => [
				enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::BIGINT,
				enums\enum_orm_fieldconfigparam::attributes => enums\enum_mysqlfield_attributes::UNSIGNED,
				enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::INDEX,
				enums\enum_orm_fieldconfigparam::comment => 'Author of record'
			],
			'meta_dictionary' => [
				enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::VARCHAR4096,
				enums\enum_orm_fieldconfigparam::comment => 'External reference'
			],
			'meta_expired_statement' => [
				enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::VARCHAR10240,
				enums\enum_orm_fieldconfigparam::comment => 'Expired description'
			],
			'meta_domain_swift' => [
				enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::VARCHAR1024,
				enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::INDEX,
				enums\enum_orm_fieldconfigparam::comment => 'Interest and domain'
			],
			'meta_binary_statement' => [
				enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::VARCHAR2048,
				enums\enum_orm_fieldconfigparam::comment => 'Binary file path'
			]
		];

		$this->fieldconfigs = array_merge($fieldconfigs, $this->fieldconfigs);

		foreach ($this->fktablenames as $fkfieldname => $fktablename) {
			$fkfieldconfig = [
				enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::BIGINT,
				enums\enum_orm_fieldconfigparam::attributes => enums\enum_mysqlfield_attributes::UNSIGNED,
				enums\enum_orm_fieldconfigparam::nullable => false,
				enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::INDEX,
				enums\enum_orm_fieldconfigparam::comment => 'Foreign key to `' . $fktablename . '`'
			];
			$this->fieldconfigs = array_merge($this->fieldconfigs, [$fkfieldname => $fkfieldconfig]);
		}
	}

	public function get_tablename(): string {
		return $this->tablename;
	}

	public function create_table(): bool {
		try {
			$liquor = $this->liquor_create_table();

			$mysqlstatement = '';

			foreach ($liquor as $net => $lint) {
				if (count($lint) > 0) {
					switch ($net) {
						case 'prefixes':
						case 'suffixes':
							$mysqlstatement .= implode(' ', $lint);
							break;
						case 'fields':
							foreach ($lint as $tin) {
								$mysqlstatement .= implode(' ', $tin) . ', ';
							}
							break;
						case 'indexes':
							$mysqlstatement .= implode(', ', $lint);
							break;
						case 'fks':
							$mysqlstatement .= ', ' . implode(', ', $lint);
					}
				}
			}
			
			$result = $this->job_db->mysqlexec($mysqlstatement);
			if (isset($result)) {
				return true;
			}
		}
		catch (Exception $exception) {
			return false;
		}

		return false;
	}

	public function liquor_create_table(): ?array {
		try {
			$liquor = ['prefixes' => [], 'fields' => [], 'indexes' => [], 'fks' => [], 'suffixes' => []];

			$liquor['prefixes'] = ['CREATE TABLE `' . $this->tablename . '`', '('];

			foreach ($this->fieldconfigs as $fieldname => $fieldconfig) {
				$name = isset($fieldname) && !empty($fieldname)
					? '`' . $fieldname . '`'
					: false;
				$type = isset($fieldconfig[enums\enum_orm_fieldconfigparam::type]) && isset($this->mysqlfield_type_signatures[$fieldconfig[enums\enum_orm_fieldconfigparam::type]])
					? $this->mysqlfield_type_signatures[$fieldconfig[enums\enum_orm_fieldconfigparam::type]]
					: false;

				if ($type) {
					$attributes = '';
					if (
						isset($fieldconfig[enums\enum_orm_fieldconfigparam::attributes])
						&& isset($this->mysqlfield_attributes_signatures[$fieldconfig[enums\enum_orm_fieldconfigparam::attributes]])
					) {
						$attributes = $this->mysqlfield_attributes_signatures[$fieldconfig[enums\enum_orm_fieldconfigparam::attributes]];
					}

					$nullable = '';
					if (isset($fieldconfig[enums\enum_orm_fieldconfigparam::nullable]) && $fieldconfig[enums\enum_orm_fieldconfigparam::nullable] === false) {
						$nullable = 'NOT NULL';
					} else {
						$nullable = 'NULL';
					}

					$autoincrement = '';
					if (isset($fieldconfig[enums\enum_orm_fieldconfigparam::autoincrement]) && $fieldconfig[enums\enum_orm_fieldconfigparam::autoincrement] === true) {
						$autoincrement = 'AUTO_INCREMENT';
					}

					$default = '';
					if (isset($fieldconfig[enums\enum_orm_fieldconfigparam::default])) {
						if (isset($this->mysqlfield_default_signatures[$fieldconfig[enums\enum_orm_fieldconfigparam::default]])) {
							$default = 'DEFAULT ' . $this->mysqlfield_default_signatures[$fieldconfig[enums\enum_orm_fieldconfigparam::default]];
						} else {
							$default = 'DEFAULT \'' . $fieldconfig[enums\enum_orm_fieldconfigparam::default] . '\'';
						}
					}

					$index = isset($fieldconfig[enums\enum_orm_fieldconfigparam::index]) && isset($this->mysqlfield_index_signatures[$fieldconfig[enums\enum_orm_fieldconfigparam::index]])
						? $this->mysqlfield_index_signatures[$fieldconfig[enums\enum_orm_fieldconfigparam::index]]
						: false;

					$comment = '';
					if (isset($fieldconfig[enums\enum_orm_fieldconfigparam::comment]) && !empty($fieldconfig[enums\enum_orm_fieldconfigparam::comment])) {
						$comment = 'COMMENT \'' . $fieldconfig[enums\enum_orm_fieldconfigparam::comment] . '\'';
					}

					array_push($liquor['fields'], [
						enums\enum_orm_fieldconfigparam::name => $name,
						enums\enum_orm_fieldconfigparam::type => $type,
						enums\enum_orm_fieldconfigparam::attributes => $attributes,
						enums\enum_orm_fieldconfigparam::nullable => $nullable,
						enums\enum_orm_fieldconfigparam::autoincrement => $autoincrement,
						enums\enum_orm_fieldconfigparam::default => $default,
						enums\enum_orm_fieldconfigparam::comment => $comment
					]);

					if ($index !== false) {
						array_push($liquor['indexes'], $index . ' (' . $name . ')');
					}
				}
			}

			foreach ($this->fktablenames as $fkfieldname => $fktablename) {
				$constraintname = 'fk_' . $this->tablename . '_' . $fktablename . '_meta_id';
				$e = 'CONSTRAINT `' . $constraintname . '` FOREIGN KEY(`' . $fkfieldname . '`) REFERENCES `' . $fktablename . '`(`meta_id`) ON DELETE CASCADE ON UPDATE RESTRICT';
				array_push($liquor['fks'], $e);
			}

			$liquor['suffixes'] = [')', 'ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_unicode_ci;'];

			return $liquor;
		}
		catch (Exception $exception) {
			throw new job_exception('Table \'' . $this->tablename . '\' couldn\'t be created.', $exception);
		}
		return null;
	}

	public function get_map() {
		try {
			$map = new mapper($this->job_db->retrieve_handle(), $this->tablename);
			return $map;
		}
		catch (Exception $exception) {
			throw new job_exception('Unable to create map.', $exception);
		}
		return null;
	}

}
