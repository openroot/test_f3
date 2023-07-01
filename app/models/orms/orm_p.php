<?php

namespace models\orms;

use \DB\SQL\Schema as Schema;
use \models\abstracts\abstract_orm as abstract_orm;
use \models\enums\enum_mysql_datatype as enum_mysql_datatype;

class orm_p extends abstract_orm {
	protected $field_configurations = [
		'created_at' => [
			'type' => enum_mysql_datatype::TIMESTAMP,
			'nullable' => false,
			'default' => Schema::DF_CURRENT_TIMESTAMP
		],
		'name' => [
			'type' => enum_mysql_datatype::VARCHAR128,
			'nullable' => false,
			'index' => true
		],
		'description' => [
			'type' => enum_mysql_datatype::TEXT,
			'nullable' => true
		],
		'privateid' => [
			'type' => enum_mysql_datatype::VARCHAR512,
			'nullable' => true,
			'unique' => true
		]
	],
	$table_name = 'p';
}
