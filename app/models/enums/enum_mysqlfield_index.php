<?php

namespace models\enums;

use \models\abstracts\abstract_enum as abstract_enum;

class enum_mysqlfield_index extends abstract_enum {
	public const PRIMARYKEY = 'PRIMARYKEY';
	public const INDEX = 'INDEX';
	public const UNIQUE = 'UNIQUE';
}