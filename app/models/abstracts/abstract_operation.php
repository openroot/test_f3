<?php


namespace models\abstracts;

use Exception as Exception;
use \Template as Template;
use \jobs\job_exception as job_exception;

use \jobs\job_db as job_db;
use \models\enums as enums;

abstract class abstract_operation extends abstract_model {
	protected Template $f3template;
	protected job_db $job_db;

	public function __construct() {
		try {
			$this->f3template = Template::instance();
		}
		catch (Exception $exception) {
			throw new job_exception('F3 Template instance couldn\'t be initialized.', $exception);
			return false;
		}
		try {
			$this->job_db = new job_db($this->f3, enums\enum_database_type::f3mysql);
		}
		catch (Exception $exception) {
			throw new job_exception('Database job couldn\'t be initialized.', $exception);
			return false;
		}
		return true;
	}
}