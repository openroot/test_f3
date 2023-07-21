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

		$htmlstring .= job_rough::get_htmlstring_jumbotron31('Management', 'Basic operations', job_rough::get_htmlstring_anchorbutton11('#', 'Add another'));
		$htmlstring .= job_rough::get_htmlstring_hr3();

		$rows = [];
		foreach (job_rough::get_list_ormclass() as $index => $ormclass) {
			$htmlstring1 = job_rough::get_htmlstring_anchorbutton12('/instruct/orm/explore/orm_' . $ormclass['mysqltable'], $ormclass['phpclass']);
			array_push($rows, [($index + 1), $htmlstring1]);
		}
		$htmlstring .= job_rough::get_htmlstring_table(['Index', 'PHP Class'], $rows, 'ORM Classes Ordered', 'table table-hover table-borderless background-light');

		$f3->ormgenerator_default += ['htmlstring' => $htmlstring];

		$this->render();
	}
}