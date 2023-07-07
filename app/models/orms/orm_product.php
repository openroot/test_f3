<?php

namespace models\orms;

use \models\abstracts\abstract_orm as abstract_orm;
use \models\enums as enums;

class orm_product extends abstract_orm {
	protected $fieldconfigs = [
		'name' => [
			enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::VARCHAR512,
			enums\enum_orm_fieldconfigparam::nullable => false,
			enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::INDEX,
			enums\enum_orm_fieldconfigparam::comment => 'Product name'
		],
		'description' => [
			enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::TEXT,
			enums\enum_orm_fieldconfigparam::comment => 'Product description'
		],
		'mrp' => [
			enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::DOUBLE,
			enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::INDEX,
			enums\enum_orm_fieldconfigparam::comment => 'Maximum price'
		],
		'privateid' => [
			enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::VARCHAR512,
			enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::UNIQUE,
			enums\enum_orm_fieldconfigparam::comment => 'Product private identifier'
		],
		'publicid' => [
			enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::VARCHAR512,
			enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::UNIQUE,
			enums\enum_orm_fieldconfigparam::comment => 'Product public identifier'
		]
	];
	protected $fkconfigs = [orm_brand::class];
}
