<?php

namespace operations;

use \Base as Base;
use \models\abstracts\abstract_operation as abstract_operation;
use \jobs\job_rough as job_rough;

class operation_ormgenerator extends abstract_operation {
	public function __construct() {
		parent::__construct();
	}

	public function ormgenerator_default(Base $f3) {
		$htmlstring = '';
		$rows = [];
		foreach (job_rough::get_ormclass_orderedlist() as $index => $ormclass) {
			$htmlstring1 = job_rough::get_htmlstring_anchorbutton(
				'/instruct/orm/explore/orm_' . job_rough::extract_tablename_from_ormclassname($ormclass),
				$ormclass,
				'btn btn-outline-danger'
			);

			array_push($rows, [($index + 1), $htmlstring1]);
		}
		$htmlstring .= job_rough::get_htmlstring_table(['Index', 'PHP Class'], $rows, 'ORM Classes Ordered', 'table table-hover table-borderless background-light');

		$f3->ormgenerator_default += ['htmlstring' => $htmlstring];

		$this->render();
	}
}
