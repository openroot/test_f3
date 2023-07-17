<?php

namespace app;

use Exception as Exception;
use \Base as Base;
use \Template as Template;

require(__DIR__ . '/../vendor/autoload.php');

class app_test_f3 {
	private ?Base $handle_this = null;

	private string $config_app_name = '';

	public function __construct(string $app_name) {
		$this->config_app_name = $app_name;

		if ($this->validate_config()) {
			return $this->handshake();
		}
		return false;
	}

	private function validate_config(): bool {
		if (!(isset($this->config_app_name) && !empty($this->config_app_name))) {
			echo 'Package Exception: App name is invalid. EOL';
		}

		return true;
	}

	private function handshake(): bool {
		try {
			$this->handle_this = Base::instance();
			$this->handle_this->config('../app/resources/setup.cfg');
		}
		catch (Exception $exception) {
			$this->destroy_handle();
			echo 'Package Exception: F3 framework couldn\'t be instantiated. [ ' . $exception->getMessage() . ' ]. EOL';
		}

		if ($this->issuccess_init()) {
			if ($this->set_globalvalues()) {
				if ($this->prepare_routes()) {
					if ($this->publish_app()) {
						return true;
					}
					else {
						$this->destroy_handle();
					}
				}
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

	public function retrieve_handle(): Base {
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

	private function set_globalvalues(): bool {
		if ($this->issuccess_init()) {
			try {
				$this->handle_this->AUTOLOAD = $this->handle_this->get('f3app.autoload');
				$this->handle_this->DEBUG = $this->handle_this->get('f3app.debuglevel');
				$this->handle_this->GUI = $this->handle_this->get('f3app.gui');

				$this->handle_this->sitename = $this->handle_this->get('sitename');
				$this->handle_this->appname = $this->handle_this->get('appname');
				$this->handle_this->segmentspath = $this->handle_this->get('segments.path');
				$this->handle_this->segmentsdefaultrender = $this->handle_this->get('segments.defaultrender');
				$this->handle_this->segmentsrender = $this->handle_this->get('segments.render');
				$this->handle_this->transactionsblobspath = $this->handle_this->get('transactions.blobs.path');

				$this->handle_this->externallink = 'window.open(this.href); return false;';
				$this->handle_this->modelsormsbreadcrumb = $this->handle_this->get('models.orms.breadcrumb') . '\\';

				return true;
			}
			catch (Exception $exception) {
				$this->destroy_handle();
				echo 'Package Exception: Configurations couldn\'t be initiated. [ ' . $exception->getMessage() . ' ]. EOL';
			}
		}
		return false;
	}

	private function prepare_routes(): bool {
		if ($this->issuccess_init()) {
			try {
				$f3 = $this->handle_this;

				// URI example: http://localhost:4000/
				$this->handle_this->route(
					'GET '.
					'@main_default: '.
					'/',
					function ($f3) {
						// faster inbuilt function realization
						$f3->main_default = [];

						$f3->segmentsrender = 'segment/operation/main/default.htm';
						echo Template::instance()->render($f3->segmentsdefaultrender);
					}
				);

				// URI example: http://localhost:4000/template/
				$this->handle_this->route(
					'GET ' .
					'@main_template_default: ' .
					'/template',
					'operations\operation_main->main_template_default'
				);

				// URI example: http://localhost:4000/helloworld/D Tapader/34/Software Engineer
				$this->handle_this->route(
					'GET|POST '.
					'@main_helloworld_getpost: '.
					'/helloworld/@name/@age/@profession',
					'operations\operation_main->main_helloworld_getpost'
				);

				// URI example: http://localhost:4000/f3jig/
				$this->handle_this->route(
					'GET '.
					'@main_f3jig_default: '.
					'/f3jig',
					'operations\operation_main->main_f3jig_default'
				);

				// URI example: http://localhost:4000/f3mysql/
				$this->handle_this->route(
					'GET ' .
					'@main_f3mysql_default: ' .
					'/f3mysql',
					'operations\operation_main->main_f3mysql_default'
				);

				// URI example: http://localhost:4000/db/
				$this->handle_this->route(
					'GET ' .
					'@main_db_default: ' .
					'/db',
					'operations\operation_main->main_db_default'
				);

				// URI example: http://localhost:4000/info/
				$this->handle_this->route(
					'GET ' .
					'@info_default: ' .
					'/info',
					'operations\operation_info->info_default'
				);

				// URI example: http://localhost:4000/info/about
				$this->handle_this->route(
					'GET ' .
					'@info_about_default: ' .
					'/info/about',
					'operations\operation_info->info_about_default'
				);

				// URI example: http://localhost:4000/instruct/
				$this->handle_this->route(
					'GET ' .
					'@instruct_default: ' .
					'/instruct',
					'operations\operation_instruct->instruct_default'
				);

				// URI example: http://localhost:4000/instruct/orm
				$this->handle_this->route(
					'GET ' .
					'@instruct_orm_default: ' .
					'/instruct/orm',
					'operations\operation_instruct->instruct_orm_default'
				);

				// URI example: http://localhost:4000/instruct/orm/explore/orm_orde
				$this->handle_this->route(
					'GET ' .
					'@instruct_orm_explore_default: ' .
					'/instruct/orm/explore/@ormclass',
					'operations\operation_instruct->instruct_orm_explore_default'
				);

				// URI example: http://localhost:4000/instruct/orm/execute
				$this->handle_this->route(
					'GET ' .
					'@instruct_orm_execute_default: ' .
					'/instruct/orm/execute',
					'operations\operation_instruct->instruct_orm_execute_default'
				);

				// URI example: http://localhost:4000/instruct/orm/litter
				$this->handle_this->route(
					'GET ' .
					'@instruct_orm_litter_default: ' .
					'/instruct/orm/litter',
					'operations\operation_instruct->instruct_orm_litter_default'
				);

				// URI example: http://localhost:4000/instruct/orm/litter/seed
				$this->handle_this->route(
					'GET ' .
					'@instruct_orm_litter_seed_default: ' .
					'/instruct/orm/litter/seed',
					'operations\operation_instruct->instruct_orm_litter_seed_default'
				);

				// URI example: http://localhost:4000/ormgenerator/
				$this->handle_this->route(
					'GET ' .
					'@ormgenerator_default: ' .
					'/ormgenerator',
					'operations\operation_ormgenerator->ormgenerator_default'
				);

				// URI example: http://localhost:4000/modulegenerator/
				$this->handle_this->route(
					'GET ' .
					'@modulegenerator_default: ' .
					'/modulegenerator',
					'operations\operation_modulegenerator->modulegenerator_default'
				);

				return true;
			}
			catch (Exception $exception) {
				$this->destroy_handle();
				echo 'Package Exception: Routes couldn\'t be prepared. [ ' . $exception->getMessage() . ' ]. EOL';
			}
		}
		return false;
	}

	private function publish_app(): bool {
		if ($this->issuccess_init()) {
			try {
				$this->handle_this->run();

				return true;
			}
			catch (Exception $exception) {
				$this->destroy_handle();
				echo 'Package Exception: App were unable to be published. [ ' . $exception->getMessage() . ' ]. EOL';
			}
		}
		return false;
	}
}

$app_test_f3 = new app_test_f3('Test F3');
if ($app_test_f3->issuccess_init()) {
	exit(0);
}
else {
	exit(1);
}
