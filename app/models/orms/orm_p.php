<?php

namespace models\orms;

use \models\abstracts\abstract_orm as abstract_orm;
use \models\enums as enums;

class orm_p extends abstract_orm {
	protected $field_configurations = [
		'created_at' => [
			'type' => enums\enum_mysql_datatype::TIMESTAMP,
			'nullable' => false,
			'default' => enums\enum_mysql_defaulttype::CURRENT_TIMESTAMP
		],
		'name' => [
			'type' => enums\enum_mysql_datatype::VARCHAR128,
			'nullable' => false,
			'index' => true
		],
		'description' => [
			'type' => enums\enum_mysql_datatype::TEXT,
			'nullable' => true
		],
		'privateid' => [
			'type' => enums\enum_mysql_datatype::VARCHAR512,
			'nullable' => true,
			'unique' => true
		]
	],
	$table_name = 'p';
}
