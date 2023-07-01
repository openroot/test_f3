<?php

namespace models\abstracts;

use Exception as Exception;
use \Base as Base;
use \jobs\job_exception as job_exception;

abstract class abstract_model {
	protected ?Base $f3 = NULL;

	public function __construct() {
		if (!isset($this->f3)) {
			try {
				$this->f3 = Base::instance();

				return true;
			}
			catch (Exception $exception) {
				throw new job_exception('F3 base instance couldn\'t be initialized.', $exception);
			}
		}
		return false;
	}
}