<?php

namespace operations;

use Exception as Exception;
use \Base as Base;
use \models\abstracts\abstract_operation as abstract_operation;
use \models\orms as orms;

class operation_instruct extends abstract_operation {
	public function __construct() {
		parent::__construct();
	}

	public function instruct_default(Base $f3): bool {
		$f3->instruct_default = [];

		$this->render();
		return true;
	}

	public function instruct_tryinstall_default(Base $f3): bool {
		$f3->instruct_tryinstall_default = [];

		$html1 = '';

		$orm_prod = new orms\orm_prod();

		$liquor = $orm_prod->liquor_create_table();
		if (isset($liquor)) {
			$html1 .= $orm_prod->get_html_table('Prefixes', $liquor['prefixes'], $orm_prod->get_tablename());
			$html1 .= $orm_prod->get_html_table(['Field-name', 'Type', 'Attributes', 'Is-null', 'Auto-increment', 'Default-value', 'Comment'], $liquor['fields']);
			$html1 .= $orm_prod->get_html_table('Indexes', $liquor['indexes']);
			$html1 .= $orm_prod->get_html_table('Suffixes', $liquor['suffixes']);
		}

		$f3->instruct_tryinstall_default += ['html1' => $html1];

		$this->render();
		return true;
	}
}
