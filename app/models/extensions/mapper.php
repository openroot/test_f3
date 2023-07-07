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
		return $this->load($filter, $options, $ttl);
		if ($depthlevel > -1) {

		}
		else {
			throw new job_exception('Depth level of foreign data must be above 0');
		}
	}
}