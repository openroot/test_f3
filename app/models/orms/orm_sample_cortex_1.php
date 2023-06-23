<?php

namespace models\orms;

use \DB\SQL\Schema as Schema;
use \DB\Cortex as Cortex;

class orm_sample_cortex_1 extends Cortex {
	protected
		$fieldConf = [
			'created_at' => [
				'type' => Schema::DT_TIMESTAMP,
				'nullable' => false,
				'default' => Schema::DF_CURRENT_TIMESTAMP
			],
			'name' => [
				'type' => Schema::DT_VARCHAR128,
				'nullable' => false
			],
			'email' => [
				'type' => Schema::DT_VARCHAR128,
				'nullable' => false,
				'index' => true,
				'unique' => true
			],
			'website' => [
				'type' => Schema::DT_VARCHAR128,
				'nullable' => true
			],
			'valid' => [
				'type' => Schema::DT_TINYINT,
				'nullable' => false,
				'default' => 1
			],
			'address' => [
				'type' => self::DT_JSON,
				'nullable' => true
			]
		],
		$db = 'DB1',
		$table = 'sample_cortex_1',
		$primary = 'id'; // Name of the primary key (auto-created), default: id.
}