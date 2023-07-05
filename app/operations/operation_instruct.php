<?php

namespace operations;

use \Base as Base;
use \models\abstracts\abstract_operation as abstract_operation;
use \jobs\job_rough as job_rough;

class operation_instruct extends abstract_operation {
	private array $orm_liquor_identifiers = [
		'prefixes' => 'Prefixes',
		'fields' => ['Field name', 'Type', 'Attributes', 'Is null', 'Auto increment', 'Default value', 'Comment'],
		'indexes' => 'Indexes',
		'fks' => 'Foreign keys',
		'suffixes' => 'Suffixes',
	];

	public function __construct() {
		parent::__construct();
	}

	public function instruct_default(Base $f3): bool {
		$f3->instruct_default = [];

		$this->render();
		return true;
	}

	public function instruct_orm_default(Base $f3): bool {
		$f3->instruct_orm_default = [];

		$htmlstring = '';
		$rows = [];
		foreach (job_rough::get_ormclass_orderedlist() as $index => $ormclass) {
			array_push($rows, [($index + 1), $ormclass]);
		}
		$htmlstring .= job_rough::get_htmlstring_table(['Index', 'Class name'], $rows, 'Ordered list of orm classes');

		$f3->instruct_orm_default += ['htmlstring' => $htmlstring];

		$this->render();
		return true;
	}

	public function instruct_orm_explore_default(Base $f3): bool {
		$f3->instruct_orm_explore_default = [];

		$qs_ormclass = $f3->PARAMS['ormclass'];

		$htmlstring = '';

		$namespace = '\models\orms\\';
		$orminstance = job_rough::get_instance_class($qs_ormclass, '\models\orms');
		if (isset($orminstance)) {
			$orm_liquor = $orminstance->liquor_create_table();
			$htmlstring .= job_rough::get_liquorinto_htmlstring_table($orm_liquor, $this->orm_liquor_identifiers);
		}
		else {
			$htmlstring .= 'ORM Class \'' . $namespace . $qs_ormclass . '\' not found.';
		}

		$f3->instruct_orm_explore_default += ['htmlstring' => $htmlstring];

		$this->render();
		return true;
	}

	public function instruct_orm_execute_default(Base $f3): bool {
		$f3->instruct_orm_execute_default = [];

		$htmlstring = '';

		$rows = [];
		foreach (job_rough::get_ormclass_orderedlist() as $index => $ormclass) {
			$orminstance = new $ormclass();

			$orm_liquor = $orminstance->liquor_create_table();

			array_push($rows, '<h2>' . ($index + 1) . '. ' . $orminstance->get_tablename() . '</h2>');
			array_push($rows, job_rough::get_liquorinto_htmlstring_table($orm_liquor, $this->orm_liquor_identifiers));
			if ($orminstance->create_table()) {
				array_push($rows, '<div class="positivetext">Table \''. $orminstance->get_tablename() . '\' created.</div>');
			}
			else {
				array_push($rows, '<div class="negativetext">Table \'' . $orminstance->get_tablename() . '\' created.</div>');
			}
		}

		$htmlstring .= job_rough::get_htmlstring_table('List of tables', $rows);

		$f3->instruct_orm_execute_default += ['htmlstring' => $htmlstring];

		$this->render();
		return true;
	}

}