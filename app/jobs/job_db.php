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
	private ?bool $handle_this = NULL;
	private Base $config_f3;
	private string $config_enum_database_type = '';
	private ?string $config_database_id = '';
	private ?SQL $handle_f3msql = NULL;

	public function __construct(Base $f3, string $enum_database_type, ?string $database_id = NULL) {
		$this->config_f3 = $f3;
		$this->config_enum_database_type = $enum_database_type;
		$this->config_database_id = $database_id;

		if ($this->validate_config()) {
			return $this->handshake();
		}
		return false;
	}

	private function validate_config(): bool {
		if (isset($this->config_f3)) {
			if (isset($this->config_enum_database_type)
				// TODO: Add enum existance check.
			) {
				return true;
			}
			else {
				throw new job_exception('Database type is invalid.');
			}
		}
		else {
			throw new job_exception('F3 instance is null.');
		}
		return false;
	}

	private function handshake(): bool {
		$this->config_database_id = isset($this->config_database_id) && !empty($this->config_database_id) ? $this->config_database_id : $this->config_f3->get('job.db.default.id');
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
					throw new job_exception('Database type is not valid.');
					break;
			}

			$this->handle_this = true;

			return true;
		}
		catch (Exception $exception) {
			$this->destroy_handle();
			throw new job_exception('Database unable to initialized.', $exception);
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

	public function retrieve_handle(): ?bool {
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

	public function get_modelsorms_breadcrumb(?string $modelsorms_breadcrumb = NULL): string {
		return isset($modelsorms_breadcrumb) && !empty($modelsorms_breadcrumb) ? $modelsorms_breadcrumb . '\\' : $this->config_f3->get('modelsormsbreadcrumb');
	}

	public function get_modelsorms_names(string $directory, ?string $modelsorms_breadcrumb = NULL): array {
		$modelsorms_names = array();

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

	public function create_table($modelorm_name, ?string $modelsorms_breadcrumb = NULL): bool {
		if ($this->issuccess_init()) {
			try {
				$modelsorms_breadcrumb = $this->get_modelsorms_breadcrumb($modelsorms_breadcrumb);
				$modelorm = $modelsorms_breadcrumb . $modelorm_name;
				$modelorm::setup();
				return true;
			}
			catch (Exception $exception) {
				throw new job_exception('Table Couldn\'t created.', $exception);
			}
		}
		return false;
	}

	public function create_tables(string $directory, ?string $modelsorms_breadcrumb = NULL): bool {
		try {
			foreach ($this->get_modelsorms_names($directory, $modelsorms_breadcrumb) as $modelorm_name) {
			 	$this->create_table($modelorm_name, $modelsorms_breadcrumb);
			}
			return true;
		}
		catch (Exception $exception) {
			throw new job_exception('Tables Couldn\'t created.', $exception);
		}
		return false;
	}

	public function f3mysql_execute(string $mysql_statement) {
		if ($this->issuccess_init()) {
			if (isset($mysql_statement) && !empty($mysql_statement)) {
				try {
					return $this->handle_f3msql->exec($mysql_statement);
				}
				catch (Exception $exception) {
					throw new job_exception("MySQL were unable to execute.", $exception);
				}
			}
		}
		return NULL;
	}
}
