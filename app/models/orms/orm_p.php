<?php

namespace models\orms;

use \DB\SQL\Schema as Schema;
use \models\abstracts\abstract_orm as abstract_orm;

class orm_p extends abstract_orm {
	protected $field_configurations = [
		'created_at' => [
			'type' => Schema::DT_TIMESTAMP,
			'nullable' => false,
			'default' => Schema::DF_CURRENT_TIMESTAMP
		],
		'name' => [
			'type' => Schema::DT_VARCHAR128,
			'nullable' => false
		]
		],
	$table = 'p',
	$primary = 'id'; // Name of the primary key (auto-created), default: id.
}
