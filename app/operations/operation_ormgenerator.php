<?php

namespace operations;

use \Base as Base;
use \models\abstracts\abstract_operation as abstract_operation;
use \models\enums as enums;

class operation_ormgenerator extends abstract_operation {
	public function __construct() {
		parent::__construct();
	}

	public function ormgenerator_default(Base $f3) {
		$this->render();
	}
}
