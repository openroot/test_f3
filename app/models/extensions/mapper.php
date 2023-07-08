<?php

namespace models\extensions;

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
	 * @param integer|null $depthlevel : null is maximum possible. Rest depth levels from 1 are interim, defaulted to first most level.
	 */
	public function load_withfkdata($filter = null, array $options = null, $ttl = 0, ?int $depthlevel = 1) {
		$e = false;

		if ($depthlevel > -1) {
			$e = $this->load($filter, $options, $ttl);
			$this->load_end($ttl, $depthlevel - 1);
		}
		else if ($depthlevel === null) {
			$e = $this->load($filter, $options, $ttl);
			$this->load_end($ttl, null);
		}

		return $e;
	}

	private function load_end($ttl, ?int $depthlevel) {
		$fields = $this->fields();
		foreach ($fields as $field) {
			$isfk = strpos($field, 'fk_');
			if ($isfk !== false && $isfk === 0) {
				$foreigntablename = str_replace('_meta_id', '', str_replace('fk_', '', $field));
				$foreigntabletruename = '\models\orms\orm_' . $foreigntablename;

				$map = (new $foreigntabletruename())->get_map();
				$map->load_withfkdata(['meta_id=?', $this->meta_id], null, $ttl, $depthlevel);
				$this->__set($foreigntablename, $map);
			}
		}
	}
}