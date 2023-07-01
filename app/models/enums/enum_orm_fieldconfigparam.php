<?php

namespace models\enums;

use \models\abstracts\abstract_enum as abstract_enum;

class enum_orm_fieldconfigparam extends abstract_enum {
	public const type = 'type';						// 1. enum_mysqlfield_type::option (mandatory)
	public const attributes = 'attributes';			// 2. enum_mysqlfield_attributes::option
	public const nullable = 'nullable';				// 3. true or false
	public const autoincrement = 'autoincrement';	// 4. true or false
	public const default = 'default';				// 5. enum_mysqlfield_default::option or primitive value
	public const index = 'index';					// 6. enum_mysqlfield_index::option
	public const comment = 'comment';				// 7. string
}