<?php

namespace models\abstracts;

use Exception as Exception;
use \Base as Base;
use \jobs\job_exception as job_exception;

abstract class abstract_model {
	protected Base $f3;

	public function __construct() {
		try {
			$this->f3 = Base::instance();
		}
		catch (Exception $exception) {
			throw new job_exception('F3 Base instance couldn\'t be initialized.', $exception);
			return false;
		}
		return true;
	}
}