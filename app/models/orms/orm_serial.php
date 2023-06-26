<?php

namespace models\orms;

use \DB\SQL\Schema as Schema;
use \DB\Cortex as Cortex;

class orm_serial extends Cortex {
	protected
		$fieldConf = [
			'created_at' => [
				'type' => Schema::DT_TIMESTAMP,
				'nullable' => false,
				'default' => Schema::DF_CURRENT_TIMESTAMP
			],
			'id_order' => [
				'type' => Schema::DT_INT4,
				'nullable' => true
			]
		],
		$db = 'DB1',
		$table = 'serial',
		$primary = 'id'; // Name of the primary key (auto-created), default: id.
}