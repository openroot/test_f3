<?php

namespace models\orms;

use \DB\SQL\Schema as Schema;
use \DB\Cortex as Cortex;

class orm_customer extends Cortex {
	protected
		$fieldConf = [
			'created_at' => [
				'type' => Schema::DT_TIMESTAMP,
				'nullable' => false,
				'default' => Schema::DF_CURRENT_TIMESTAMP
			],
			'name' => [
				'type' => Schema::DT_VARCHAR128,
				'nullable' => false,
				'default' => ''
			],
			'phone_number' => [
				'type' => Schema::DT_VARCHAR128,
				'nullable' => true,
				'unique' => true
			],
			'address' => [
				'type' => self::DT_JSON,
				'nullable' => true
			]
		],
		$db = 'DB1',
		$table = 'customer',
		$primary = 'id'; // Name of the primary key (auto-created), default: id.
}