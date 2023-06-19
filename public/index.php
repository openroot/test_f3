<?php

namespace app;

use Exception as Exception;
use \Base as Base;
use \Template as Template;

require(__DIR__ . '/../vendor/autoload.php');

class test_f3 {
	private ?object $handle_this = NULL;
	private string $config_app_name = '';

	public function __construct(string $app_name) {
		$this->config_app_name = $app_name;

		if ($this->validate_config()) {
			return $this->handshake();
		}
		return false;
	}

	private function validate_config(): bool {
		if (isset($this->config_app_name) && !empty($this->config_app_name)) {
			return true;
		}
		else {
			echo 'Package Exception: App name is invalid. EOL';
		}
		return false;
	}

	private function handshake(): bool {
		try {
			$this->handle_this = Base::instance();
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

	public function retrieve_handle(): ?object {
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

	private function set_globalvalues(): bool {
		if ($this->issuccess_init()) {
			try {
				$this->handle_this->AUTOLOAD = '../app/';
				$this->handle_this->DEBUG = 3;
				$this->handle_this->GUI = 'gui/';

				$this->handle_this->site = $this->config_app_name;
				$this->handle_this->app = $this->config_app_name;
				$this->handle_this->segmentpath = '../app/segments/';
				$this->handle_this->segmentappdefault = $this->handle_this->segmentpath . 'segment_app_default.htm';
				$this->handle_this->segment = '';
				$this->handle_this->transactionblobpath = '../app/transactions/blob/';
				$this->handle_this->blobf3jigpath = $this->handle_this->transactionblobpath. 'f3jig/';

				$this->handle_this->externallink = 'window.open(this.href); return false;';

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

				// URI example
				// http://localhost:4000
				$this->handle_this->route(
					'GET @indexdefault: /',
					// faster inbuilt function realization
					function ($f3) {
						$f3->index_default = array(
							'value1' => 'This user-defined value.'
						);

						$f3->segment = 'segment_operation_index_default.htm';
						echo Template::instance()->render($f3->segmentappdefault);
					}
				);

				// URI example
				// http://localhost:4000/helloworld/D Tapader/34/Software Engineer
				$this->handle_this->route('GET|POST @indexhelloworld: /helloworld/@name/@age/@profession', 'operations\operation_index->helloworld_default');

				// URI example
				// http://localhost:4000/f3jig
				$this->handle_this->route('GET @indexf3jig: /f3jig', 'operations\operation_index->f3jig_default');

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

$test_f3 = new test_f3('Test F3');
if ($test_f3->issuccess_init()) {
	exit(0);
}
else {
	exit(1);
}