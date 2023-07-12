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

	public function instruct_default(Base $f3) {
		$this->render();
	}

	public function instruct_orm_default(Base $f3) {
		$htmlstring = '';
		$rows = [];
		foreach (job_rough::get_ormclass_orderedlist() as $index => $ormclass) {
			$href = '<a href="/instruct/orm/explore/orm_'
				. job_rough::extract_tablename_from_ormclassname($ormclass)
				. '">' . $ormclass . '</a>';

			array_push($rows, [($index + 1), $href]);
		}
		$htmlstring .= job_rough::get_htmlstring_table(['Index', 'Class name'], $rows, 'Ordered list of orm classes');

		$f3->instruct_orm_default += ['htmlstring' => $htmlstring];

		$this->render();
	}

	public function instruct_orm_explore_default(Base $f3) {
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
	}

	public function instruct_orm_execute_default(Base $f3) {
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
				array_push($rows, '<div class="negativetext">Table \'' . $orminstance->get_tablename() . '\' either exist or not created.</div>');
			}
		}

		$htmlstring .= job_rough::get_htmlstring_table('List of tables', $rows);

		$f3->instruct_orm_execute_default += ['htmlstring' => $htmlstring];

		$this->render();
	}

	public function instruct_orm_litter_default(Base $f3) {
		$this->render();
	}

	public function instruct_orm_litter_seed_default(Base $f3) {
		$b1 = (new \models\orms\orm_brand())->get_map();
		$p1 = (new \models\orms\orm_product())->get_map();
		$c1 = (new \models\orms\orm_customer())->get_map();
		$o1 = (new \models\orms\orm_order())->get_map();

		$o1_id = null;
		if (count($p1->find('')) === 0) {
			$b1->name = 'Cello Ltd.';
			$b1->save();

			$p1->name = 'Cello Plastic Pen';
			$p1->fk_brand_meta_id = 1;
			$p1->save();

			$p1_id = $p1->meta_id;
			$c1->fullname = 'Debaprasad Tapader';
			$c1->save();

			$c1_id = $c1->meta_id;
			$o1->quantity = 12;
			$o1->fk_product_meta_id = $p1_id;
			$o1->fk_customer_meta_id = $c1_id;
			$o1->save();

			$o1_id = $o1->meta_id;
		}
		else {
			$o1->load(['meta_id=?', 1]);
			$o1_id = $o1->meta_id;
		}

		if (isset($o1_id)) {
			$o = (new \models\orms\orm_order())->get_map();
			$o->load_withfkdata(['meta_id=?', $o1_id], null, 0, null);

			$f3->instruct_orm_litter_seed_default += ['htmlstring' => job_rough::get_htmlstring_table(
				['Order ID', 'Product', 'Customer name', 'Quantity'],
				[[$o->meta_id, $o->product->name . ' ( ' . $o->product->brand->name . ' )', $o->customer->fullname, $o->quantity]]
			)];
		}

		$this->render();
	}
}