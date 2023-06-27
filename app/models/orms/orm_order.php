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
			'product_id' => [
				'belongs-to-one' => \models\orms\orm_product::class
			],
			'customer_id' => [
				'belongs-to-one' => \models\orms\orm_customer::class
			],
			'quantity' => [
				'type' => Schema::DT_INT,
				'nullable' => true
			]
		],
		$db = 'DB1',
		$table = 'order',
		$primary = 'id'; // Name of the primary key (auto-created), default: id.
}