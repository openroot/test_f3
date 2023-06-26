<?php

namespace models\orms;

use \DB\SQL\Schema as Schema;
use \DB\Cortex as Cortex;

class orm_order extends Cortex {
	protected
		$fieldConf = [
			'created_at' => [
				'type' => Schema::DT_TIMESTAMP,
				'nullable' => false,
				'default' => Schema::DF_CURRENT_TIMESTAMP
			],
			'quantity' => [
				'type' => Schema::DT_INT,
				'nullable' => false,
				'default' => 0
			],
			'id_pid' => [
				'type' => Schema::DT_VARCHAR128,
				'nullable' => false,
				'default' => '',
				'index' => true,
				'unique' => true
			],
			'serial' => [
				'type' => Schema::DT_VARCHAR512,
				'nullable' => false,
				'default' => '',
				'index' => true,
				'unique' => true
			]
		],
		$db = 'DB1',
		$table = 'order',
		$primary = 'id'; // Name of the primary key (auto-created), default: id.
}