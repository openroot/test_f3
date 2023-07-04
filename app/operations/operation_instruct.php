<?php

namespace operations;

use \Base as Base;
use \models\abstracts\abstract_operation as abstract_operation;
use \models\orms as orms;
use \jobs\job_rough as job_rough;

class operation_instruct extends abstract_operation {
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

		$this->render();
		return true;
	}

	public function instruct_orm_explore_default(Base $f3): bool {
		$f3->instruct_orm_explore_default = [];

		$html = '';



		$f3->instruct_orm_explore_default += ['html' => $html];

		$this->render();
		return true;
	}

	public function instruct_orm_execute_default(Base $f3): bool {
		$f3->instruct_orm_execute_default = [];

		$html = '';

		$rows = [];

		$ormobjects = [];
		array_push($ormobjects, new orms\orm_prod());
		array_push($ormobjects, new orms\orm_cust());
		array_push($ormobjects, new orms\orm_orde());

		foreach ($ormobjects as $index => $ormobject) {
			$liquor = $ormobject->liquor_create_table();
			array_push($rows, '<h2>' . ($index + 1) . '. ' . $ormobject->get_tablename() . '</h2>');
			array_push($rows, $this->convert_liquor_to_htmlstring_table($liquor));

			if ($ormobject->create_table()) {
				array_push($rows, '<div class="positivetext">Table \''. $ormobject->get_tablename() . '\' created.</div>');
			}
			else {
				array_push($rows, '<div class="negativetext">Table \'' . $ormobject->get_tablename() . '\' created.</div>');
			}
		}
		$html .= job_rough::get_htmlstring_table('List of tables', $rows);

		$f3->instruct_orm_execute_default += ['html' => $html];

		$this->render();
		return true;
	}

	private function convert_liquor_to_htmlstring_table(array $liquor): string {
		$html = '';
		if (isset($liquor)) {
			$html .= job_rough::get_htmlstring_table('Prefixes', $liquor['prefixes']);
			$html .= job_rough::get_htmlstring_table(['Field-name', 'Type', 'Attributes', 'Is-null', 'Auto-increment', 'Default-value', 'Comment'], $liquor['fields']);
			$html .= job_rough::get_htmlstring_table('Indexes', $liquor['indexes']);
			$html .= job_rough::get_htmlstring_table('Foreign keys', $liquor['fks']);
			$html .= job_rough::get_htmlstring_table('Suffixes', $liquor['suffixes']);
		}
		return $html;
	}
}
