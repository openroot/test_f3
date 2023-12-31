<?php

namespace models\enums;

use \models\abstracts\abstract_enum as abstract_enum;

class enum_mysqlfield_attributes extends abstract_enum {
	public const UNSIGNED = 'UNSIGNED';
	public const ON_UPDATE_CURRENT_TIMESTAMP = 'ON_UPDATE_CURRENT_TIMESTAMP';
}