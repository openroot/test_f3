<?php

namespace operations;

use \Base as Base;
use \models\abstracts\abstract_operation as abstract_operation;
use \models\enums as enums;

class operation_info extends abstract_operation {
	public function __construct() {
		parent::__construct();
	}

	public function info_default(Base $f3) {
		$job_db = new $this->job_db($f3, enums\enum_database_type::f3mysql);
		$result = $job_db->mysqlexec('SHOW TABLES');
		if (isset($result)) {
			$f3->info_default += ['tablelist' => $result];
		}

		$this->render();
	}

	public function info_about_default(Base $f3) {
		$this->render();
	}
}
