<?php

namespace models\orms;

use \models\abstracts\abstract_orm as abstract_orm;
use \models\enums as enums;

class orm_p extends abstract_orm {
	protected $fieldconfigs = [
		'created_at' => [
			enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::TIMESTAMP,
			enums\enum_orm_fieldconfigparam::nullable => false,
			enums\enum_orm_fieldconfigparam::default => enums\enum_mysqlfield_default::CURRENT_TIMESTAMP
		],
		'name' => [
			enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::VARCHAR128,
			enums\enum_orm_fieldconfigparam::nullable => false,
			enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::INDEX
		],
		'description' => [
			enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::TEXT
		],
		'privateid' => [
			enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::VARCHAR512,
			enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::UNIQUE
		]
	];
	protected $tablename = 'p';
}
