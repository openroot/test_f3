<?php

namespace models\enums;

use \models\abstracts\abstract_enum as abstract_enum;

class enum_mysql_datatype extends abstract_enum {
	public const BOOLEAN = 'BOOLEAN';
	public const INT1 = 'INT1';
	public const INT2 = 'INT2';
	public const INT4 = 'INT4';
	public const INT8 = 'INT8';
	public const BIGINT = 'BIGINT';
	public const FLOAT = 'FLOAT';
	public const DOUBLE = 'DOUBLE';
	public const VARCHAR32 = 'VARCHAR32';
	public const VARCHAR64 = 'VARCHAR64';
	public const VARCHAR128 = 'VARCHAR128';
	public const VARCHAR256 = 'VARCHAR256';
	public const VARCHAR512 = 'VARCHAR512';
	public const VARCHAR1024 = 'VARCHAR1024';
	public const VARCHAR2048 = 'VARCHAR2048';
	public const VARCHAR4096 = 'VARCHAR4096';
	public const VARCHAR10240 = 'VARCHAR10240';
	public const TEXT = 'TEXT';
	public const LONGTEXT = 'LONGTEXT';
	public const DATE = 'DATE';
	public const DATETIME = 'DATETIME';
	public const TIMESTAMP = 'TIMESTAMP';
	public const BLOB = 'BLOB';
}