<?php

namespace models\extensions;

use \jobs\job_rough as job_rough;
use \jobs\job_exception as job_exception;
use \DB\SQL as SQL;

class mapper extends SQL\Mapper {
	function __construct(SQL $db, $table, $fields = null, $ttl = 60) {
		parent::__construct($db, $table, $fields, $ttl);
	}

	/**
	 * load_withfkdata function
	 *
	 * @param [type] $filter
	 * @param [type] $options
	 * @param integer $ttl
	 * @param integer|null $depthlevel : 0 is maximum possible. Rest depth levels are interim, defaulted to first most level.
	 */
	public function load_withfkdata($filter = null, array $options = null, $ttl = 0, ?int $depthlevel = 1) {
		$e = false;

		if ($depthlevel > -1) {
			if ($this->table() === 'order') {
				$this->__set('product_name', 'SELECT name FROM product WHERE product.meta_id=order.meta_id');
				$this->__set('customer_fullname', 'SELECT fullname FROM customer WHERE customer.meta_id=order.meta_id');
			}
			
			// TODO: Make it dynamic recursive fetch.

			$e = $this->load($filter, $options, $ttl);
			$this->load_recurring($filter, $options, $ttl, $depthlevel, $this->table(), $this->meta_id);
		}
		else {
			throw new job_exception('Depth level of foreign data must be above 0');
		}

		return $e;
	}

	private function load_recurring($filter = null, array $options = null, $ttl = 0, int $currentdepth, string $tablename, string $meta_id) {
		if ($currentdepth === 0) {
			return;
		}
		else {
			$fields = $this->fields();
			foreach ($fields as $field) {
				$isfk = strpos($field, 'fk_');
				if ($isfk !== false && $isfk === 0) {
					$foreigntablename = str_replace('_meta_id', '', str_replace('fk_', '', $field));
					$foreigntabletruename = '\models\orms\orm_' . $foreigntablename;
					
					//echo $meta_id . '<br>';
					//echo $foreigntablename . '<br>';

					$map = (new $foreigntabletruename())->get_map();
					$e = $map->load(array('meta_id=?', $meta_id))->cast();



					//echo '<pre>'; print_r($e); echo '</pre>';
				}
			}
		}
	}
}