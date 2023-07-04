<?php

namespace models\orms;

use \models\abstracts\abstract_orm as abstract_orm;
use \models\enums as enums;

class orm_orde extends abstract_orm {
	protected $fieldconfigs = [
		'quantity' => [
			enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::INT8,
			enums\enum_orm_fieldconfigparam::attributes => enums\enum_mysqlfield_attributes::UNSIGNED,
			enums\enum_orm_fieldconfigparam::nullable => false,
			enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::INDEX,
			enums\enum_orm_fieldconfigparam::comment => 'Ordered quantity'
		]
	];
	protected $fkconfigs = [orm_prod::class, orm_cust::class];
}
