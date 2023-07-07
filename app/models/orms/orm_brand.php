<?php

namespace models\orms;

use \models\abstracts\abstract_orm as abstract_orm;
use \models\enums as enums;

class orm_brand extends abstract_orm {
	protected $fieldconfigs = [
		'name' => [
			enums\enum_orm_fieldconfigparam::type => enums\enum_mysqlfield_type::VARCHAR512,
			enums\enum_orm_fieldconfigparam::nullable => false,
			enums\enum_orm_fieldconfigparam::index => enums\enum_mysqlfield_index::INDEX,
			enums\enum_orm_fieldconfigparam::comment => 'Brand name'
		]
	];
}
