<?php
namespace app;
use Exception as Exception;
use \Base as Base;
use \Template as Template;

require(__DIR__ . '/../vendor/autoload.php');

class test_f3 {
	private string $config_app_name = '';
	private ?object $handle_f3 = NULL;

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
			$this->handle_f3 = Base::instance();
		}
		catch (Exception $exception) {
			$this->handle_f3 = NULL;
			echo 'Package Exception: F3 framework couldn\'t be instantiated. [ ' . $exception->getMessage() . ' ]. EOL';
		}

		if (isset($this->handle_f3)) {
			if ($this->set_globalvalues()) {
				if ($this->prepare_routes()) {
					if ($this->publish_app()) {
						return true;
					}
					else {
						$this->handle_f3 = NULL;
						return false;
					}
				}
			}
		}
		return false;
	}

	public function issuccess_init(): bool {
		if (isset($this->handle_f3)) {
			return true;
		}
		else {
			return false;
		}
	}

	public function retrieve_handle(): ?object {
		if (isset($this->handle_f3)) {
			return $this->handle_f3;
		}
		else {
			if ($this->validate_config() && $this->handshake()) {
				return $this->handle_f3;
			}
		}
		return NULL;
	}

	private function set_globalvalues(): bool {
		if (isset($this->handle_f3)) {
			try {
				$this->handle_f3->AUTOLOAD = '../app/';
				$this->handle_f3->DEBUG = 3;
				$this->handle_f3->GUI = 'gui/';

				$this->handle_f3->site = $this->config_app_name;
				$this->handle_f3->app = $this->config_app_name;
				$this->handle_f3->segmentpath = '../app/segments/';
				$this->handle_f3->segmentappdefault = $this->handle_f3->segmentpath . 'segment_app_default.htm';
				$this->handle_f3->segment = '';
				$this->handle_f3->transactionblobpath = '../app/transactions/blob/';

				$this->handle_f3->externallink = 'window.open(this.href); return false;';
			}
			catch (Exception $exception) {
				$this->handle_f3 = NULL;
				echo 'Package Exception: Configurations couldn\'t be initiated. [ ' . $exception->getMessage() . ' ]. EOL';
				return false;
			}
		}
		return true;
	}

	private function prepare_routes(): bool {
		if (isset($this->handle_f3)) {
			try {
				$f3 = $this->handle_f3;

				// URI example
				// http://localhost:4000
				$this->handle_f3->route(
					'GET @indexdefault: /',
					function ($f3) {
						$f3->index_default = array(
							'value1' => 'This user-defined value.'
						);

						$f3->segment = 'segment_index_default.htm';
						echo Template::instance()->render($f3->segmentappdefault);
					}
				);

				// URI example
				// http://localhost:4000/helloworld/D Tapader/34/Software Engineer
				$this->handle_f3->route('GET|POST @indexhelloworld: /helloworld/@name/@age/@profession', 'operations\operation_index->helloworld_default');

				// URI example
				// http://localhost:4000/jig
				$this->handle_f3->route('GET @indexjig: /jig', 'operations\operation_index->jig_default');
			}
			catch (Exception $exception) {
				$this->handle_f3 = NULL;
				echo 'Package Exception: Routes couldn\'t be prepared. [ ' . $exception->getMessage() . ' ]. EOL';
				return false;
			}
		}
		return true;
	}

	private function publish_app(): bool {
		if (isset($this->handle_f3)) {
			try {
				$this->handle_f3->run();
			}
			catch (Exception $exception) {
				$this->handle_f3 = NULL;
				echo 'Package Exception: App were unable to be published. [ ' . $exception->getMessage() . ' ]. EOL';
				return false;
			}
		}
		return true;
	}
}

$test_f3 = new test_f3('Test F3');
if ($test_f3->issuccess_init()) {
	exit(0);
}
else {
	exit(1);
}