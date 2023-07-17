<?php

namespace operations;

use \Base as Base;
use \models\abstracts\abstract_operation as abstract_operation;

class operation_modulegenerator extends abstract_operation {
	public function __construct() {
		parent::__construct();
	}

	public function modulegenerator_default(Base $f3) {
		$this->render();
	}
}
