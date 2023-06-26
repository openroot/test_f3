<?php

namespace models\orms;

use \DB\SQL\Schema as Schema;
use \DB\Cortex as Cortex;

class orm_instant extends Cortex {
	protected
		$fieldConf = [
			'created_at' => [
				'type' => Schema::DT_TIMESTAMP,
				'nullable' => false,
				'default' => Schema::DF_CURRENT_TIMESTAMP
			],
			'table_name' => [
				'type' => Schema::DT_VARCHAR128,
				'nullable' => true
			],
			'id_respective' => [
				'type' => Schema::DT_INT4,
				'nullable' => true,
				'index' => true,
				'unique' => true
			],
			'is_exists' => [
				'type' => Schema::DT_BOOLEAN,
				'nullable' => false,
				'default' => true
			]
		],
		$db = 'DB1',
		$table = 'instant',
		$primary = 'id'; // Name of the primary key (auto-created), default: id.
}