<?php

namespace jobs;

use \Base as Base;
use \jobs\job_db as job_db;

class job_rough {
	private ?Base $f3;

	public function __construct() { }

	public static function get_invokerfunctionname(int $level = 2): string {
		return debug_backtrace()[$level]['function'];
	}

	public static function get_instance_class(string $classname, ?string $namespace = null): mixed {
		$trueclassname = isset($namespace) ? $namespace . '\\' . $classname : $classname;
		if (class_exists($trueclassname)) {
			$instance = new $trueclassname();
			return $instance;
		}
		return null;
	}

	public static function get_ormclass_orderedlist(): array {
		$ormclass_orderedlist = [
			\models\orms\orm_prod::class,
			\models\orms\orm_cust::class,
			\models\orms\orm_orde::class
		];
		return $ormclass_orderedlist;
	}

	public static function extract_tablename_from_ormclassname(string $classname): ?string {
		if (stripos($classname, 'orm_')) {
			return substr($classname, stripos($classname, 'orm_') + 4);
		}
		return null;
	}

	public static function get_htmlstring_table($ths, $rows, ?string $caption = null, ?string $inlinestyle = null): string {
		$htmlstring = '';

		$heading_column_count = 0;
		$data_column_count = 0;

		if (is_array($ths)) {
			$heading_column_count = count($ths);
			foreach ($rows as $cells) {
				$data_column_count = $data_column_count < count($cells) ? count($cells) : $data_column_count;
			}
		}
		else {
			$heading_column_count = 1;
			$data_column_count = 1;
		}

		if (($heading_column_count > 0) && ($heading_column_count === $data_column_count)) {
			$inlinestyle = isset($inlinestyle) ? $inlinestyle : 'height: 100%';

			$htmlstring .= '<div class="viewtable" style="' . $inlinestyle . '">';
			$htmlstring .= '<table>';

			if (isset($caption)) {
				$htmlstring .= '<caption>' . $caption . '</caption>';
			}

			if ($data_column_count > 1) {
				$htmlstring .= '<tr>';
				foreach ($ths as $th) {
					$htmlstring .= '<th>' . $th . '</th>';
				}
				$htmlstring .= '</tr>';

				foreach ($rows as $cells) {
					$htmlstring .= '<tr>';
					foreach ($cells as $cell) {
						$htmlstring .= '<td>' . $cell . '</td>';
					}
					$htmlstring .= '</tr>';
				}
			}
			else {
				$htmlstring .= '<tr><th>' . $ths . '</th></tr>';
				foreach ($rows as $cell) {
					$htmlstring .= '<tr><td>' . $cell . '</td></tr>';
				}
			}

			$htmlstring .= '</table>';
			$htmlstring .= '</div>';
		}
		else {
			$htmlstring .= 'Table column head count and minimum cell count in rows is differing.';
		}

		return $htmlstring;
	}

	public static function get_liquorinto_htmlstring_table(array $liquor, array $liquor_identifiers): string {
		$htmlstring = '';

		if (isset($liquor) && isset($liquor_identifiers)) {
			if (count($liquor_identifiers) === count($liquor)) {
				foreach ($liquor_identifiers as $key => $liquor_identifier) {
					$htmlstring .= job_rough::get_htmlstring_table($liquor_identifier, $liquor[$key]);
				}
			}
		}

		return $htmlstring;
	}
}