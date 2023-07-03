<?php

namespace jobs;

use Exception;
use \Base as Base;
use \DB\SQL as SQL;
use \jobs\job_exception as job_exception;
use \transactions\transaction_f3jig as transaction_f3jig;
use \transactions\transaction_f3mysql as transaction_f3mysql;
use \models\enums as enums;

class job_db {
	private ?bool $handle_this = null;
	private ?SQL $handle_f3msql = null;

	private Base $config_f3;
	private string $config_enum_database_type = '';
	private ?string $config_database_id = null;

	public function __construct(Base $f3, string $enum_database_type, ?string $database_id = null) {
		$this->config_f3 = $f3;
		$this->config_enum_database_type = $enum_database_type;
		$this->config_database_id = $database_id;

		if ($this->validate_config()) {
			return $this->handshake();
		}
		return false;
	}

	private function validate_config(): bool {
		if (!isset($this->config_f3)) {
			throw new job_exception('F3 instance is null.');
			return false;
		}
		if (!isset($this->config_enum_database_type)) { // TODO: Check enum existance.
			throw new job_exception('Database type is invalid.');
			return false;
		}

		$this->config_database_id = isset($this->config_database_id) && !empty($this->config_database_id)
			? $this->config_database_id
			: $this->config_f3->get('job.db.default.id');

		if (!(isset($this->config_database_id) && !empty($this->config_database_id))) {
			throw new job_exception('Database ID is invalid.');
			return false;
		}

		return true;
	}

	private function handshake(): bool {
		try {
			switch ($this->config_enum_database_type) {
				case enums\enum_database_type::f3mysql:
					$this->handle_f3msql = (new transaction_f3mysql($this->config_f3))->retrieve_handle();
					$this->config_f3->set($this->config_database_id, $this->handle_f3msql);
					break;

				case enums\enum_database_type::f3jig:
					$handle_f3jig = (new transaction_f3jig($this->config_f3))->retrieve_handle();
					$this->config_f3->set($this->config_database_id, $handle_f3jig);
					break;

				default:
					throw new job_exception('Database type is invalid.');
					break;
			}

			$this->handle_this = true;
		}
		catch (Exception $exception) {
			$this->destroy_handle();
			throw new job_exception('Database unable to initialized.', $exception);
			return false;
		}
		return true;
	}

	public function issuccess_init(): bool {
		if (!isset($this->handle_this)) {
			return false;
		}
		return true;
	}

	public function retrieve_handle(): ?bool {
		if (isset($this->handle_this)) {
			return $this->handle_this;
		}
		else {
			if ($this->validate_config() && $this->handshake()) {
				return $this->handle_this;
			}
		}
		return null;
	}

	public function destroy_handle() {
		$this->handle_this = null;
	}

	public function f3mysql_execute(string $mysqlstatement) {
		if ($this->issuccess_init()) {
			if (isset($mysqlstatement) && !empty($mysqlstatement)) {
				try {
					return $this->handle_f3msql->exec($mysqlstatement);
				}
				catch (Exception $exception) {
					throw new job_exception("MySQL were unable to execute.", $exception);
				}
			}
		}
		return null;
	}

	public function get_modelsorms_breadcrumb(?string $modelsorms_breadcrumb = null): string {
		return isset($modelsorms_breadcrumb) && !empty($modelsorms_breadcrumb)
			? $modelsorms_breadcrumb . '\\'
			: $this->config_f3->get('modelsormsbreadcrumb');
	}

	public function get_modelsorms_names(string $directory, ?string $modelsorms_breadcrumb = null): array {
		$modelsorms_names = [];

		try {
			$files = array_diff(scandir($directory), array('.', '..'));
			foreach ($files as $file) {
				$model_name = substr($file, 0, -4);
				if (get_parent_class($this->get_modelsorms_breadcrumb($modelsorms_breadcrumb) . $model_name) === 'DB\Cortex') {
					array_push($modelsorms_names, $model_name);
				}
			}
		}
		catch (Exception $exception) {
			throw new job_exception('Model ORMs Couldn\'t get.', $exception);
		}

		return $modelsorms_names;
	}

	public function create_table($modelorm_name, ?string $modelsorms_breadcrumb = null): bool {
		if ($this->issuccess_init()) {
			try {
				$modelsorms_breadcrumb = $this->get_modelsorms_breadcrumb($modelsorms_breadcrumb);
				$modelorm = $modelsorms_breadcrumb . $modelorm_name;
				$modelorm::setup();
			}
			catch (Exception $exception) {
				throw new job_exception('Table Couldn\'t created.', $exception);
				return false;
			}
		}
		return true;
	}

	public function create_tables(string $directory, ?string $modelsorms_breadcrumb = null): bool {
		try {
			foreach ($this->get_modelsorms_names($directory, $modelsorms_breadcrumb) as $modelorm_name) {
			 	$this->create_table($modelorm_name, $modelsorms_breadcrumb);
			}
		}
		catch (Exception $exception) {
			throw new job_exception('Tables Couldn\'t created.', $exception);
			return false;
		}
		return true;
	}

}
