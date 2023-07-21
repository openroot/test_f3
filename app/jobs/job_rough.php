<?php

namespace jobs;

use \Base as Base;
use \transactions\transaction_f3jig as transaction_f3jig;

class job_rough {
	public function __construct() { }

	public static function extract_tablename_from_ormclassname(string $classname): ?string {
		if (stripos($classname, 'orm_')) {
			return substr($classname, stripos($classname, 'orm_') + 4);
		}
		return null;
	}

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

	public static function get_list_ormclass(): array {
		$ormclass_orderedlist = [];

		$transaction_f3jig_handle = (new transaction_f3jig(Base::instance()))->retrieve_handle();
		if (isset($transaction_f3jig_handle)) {
			$ormclasses = $transaction_f3jig_handle->read('ormclass.json');
			if (count($ormclasses) > 0) {
				foreach ($ormclasses as $ormclass) {
					array_push($ormclass_orderedlist, ['id' => $ormclass['id'], 'phpclass' => $ormclass['phpclass'], 'mysqltable' => $ormclass['mysqltable']]);
				}
			}
		}

		return $ormclass_orderedlist;
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

	public static function get_htmlstring_table($ths, $rows, ?string $caption = null, ?string $class = null, ?string $inlinestyle = null): string {
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

			$htmlstring .= '<table class="' . $class . '" style="' . $inlinestyle . '">';

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
		}
		else {
			$htmlstring .= 'Table column head count and minimum cell count in rows is differing.';
		}

		return $htmlstring;
	}

	public static function get_htmlstring_anchorbutton(string $href, string $text, ?string $button_class = null): string {
		$htmlstring = '';

		$htmlstring .= '<a href="' . $href . '"><button class="' . $button_class . '" type="button">' . $text . '</button></a>';

		return $htmlstring;
	}
}