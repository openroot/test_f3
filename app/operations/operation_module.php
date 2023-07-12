<?php

namespace operations;

use \Base as Base;
use \models\abstracts\abstract_operation as abstract_operation;

class operation_module extends abstract_operation {
	public function __construct() {
		parent::__construct();
	}

	public function module_default(Base $f3) {
		$this->render();
	}
}
