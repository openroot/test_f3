<?php

namespace operations;

use Exception as Exception;
use \Base as Base;
use \Template as Template;
use \jobs\job_exception as job_exception;
use \jobs\job_template as job_template;
use \jobs\job_db as job_db;
use \jobs\job_rough as job_rough;
use \transactions\transaction_f3jig as transaction_f3jig;
use \transactions\transaction_f3mysql as transaction_f3mysql;
use \models\enums as enums;

class operation_instruct {
	private ?string $handle_this = NULL;
	private ?Base $f3 = NULL;
	private ?Template $f3template = NULL;

	public function __construct() {
		if ($this->validate_config()) {
			return $this->handshake();
		}
		return false;
	}

	private function validate_config(): bool {
		return true;
	}

	private function handshake(): bool {
		try {
			// TODO: Put 'initialization' logics here for app client.
			$this->handle_this = 'To be replaced with real \'client\' object';
		}
		catch (Exception $exception) {
			$this->destroy_handle();
			throw new job_exception('App client unable to initialized.', $exception);
		}

		if ($this->issuccess_init()) {
			if ($this->initialize_f3singletones()) {
				// Preparing transactions, perhaps can be put in an another different operation, to avoid repeating every run main-stream.
				return $this->prepare_transactions();
			}
		}
		return false;
	}

	public function issuccess_init(): bool {
		if (isset($this->handle_this)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function retrieve_handle(): ?string {
		if (isset($this->handle_this)) {
			return $this->handle_this;
		}
		else {
			if ($this->validate_config() && $this->handshake()) {
				return $this->handle_this;
			}
		}
		return NULL;
	}

	public function destroy_handle() {
		$this->handle_this = NULL;
	}

	private function initialize_f3singletones(): bool {
		if ($this->issuccess_init()) {
			try {
				$this->f3 = Base::instance();
				$this->f3template = Template::instance();

				return true;
			}
			catch (Exception $exception) {
				$this->destroy_handle();
				throw new job_exception('F3 singletones couldn\'t be initialized.', $exception);
			}
		}
		return false;
	}

	private function prepare_transactions(): bool {
		$successchain = true;
		try {
			$job_rough = new job_rough($this->f3);

			// Preparing MySQL database, perhaps can be put in an another different operation, to avoid repeating every run main-stream.
			$job_db = new job_db($this->f3, enums\enum_database_type::f3mysql);
			if (isset($job_db) && $job_db->issuccess_init()) {
				if (!$job_rough->prepare_mysql($job_db)) {
					throw new Exception('Preparing MySQL couldn\'t be done.');
				}
			}
			else {
				throw new Exception('Database job couldn\'t be initialized.');
			}
		}
		catch (Exception $exception) {
			$successchain = false;
			throw new job_exception('Transactions couldn\'t be initialized.', $exception);
		}
		return $successchain;
	}

	public function instruct_default(Base $f3): bool {
		if ($this->issuccess_init()) {
			$f3->instruct_default = [];

			$f3->segmentsrender = 'segment_operation_instruct_default.htm';
			echo $this->f3template->render($f3->segmentsdefaultrender);

			return true;
		}
		return false;
	}

	public function instruct_tryinstall_default(Base $f3): bool {
		if ($this->issuccess_init()) {
			$f3->instruct_tryinstall_default = [];

			$html1 = '';

			$orm_p = new \models\orms\orm_p();
			$liquor = $orm_p->liquor_create_table();
			if (isset($liquor)) {
				$html1 .= $orm_p->get_html_table('Prefixes', $liquor['prefixes'], '`p`');
				$html1 .= $orm_p->get_html_table(['Field-name', 'Type', 'Attributes', 'Is-NULL', 'Auto-increment', 'Default-value', 'Comment'], $liquor['fields']);
				$html1 .= $orm_p->get_html_table('Indexes', $liquor['indexes']);
				$html1 .= $orm_p->get_html_table('Suffixes', $liquor['suffixes']);
			}

			$f3->instruct_tryinstall_default += ['html1' => $html1];

			$f3->segmentsrender = 'segment_operation_instruct_tryinstall_default.htm';
			echo $this->f3template->render($f3->segmentsdefaultrender);

			return true;
		}
		return false;
	}
}
