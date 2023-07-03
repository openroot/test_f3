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

	public static function get_htmlstring_table($ths, $rows, ?string $caption = null, ?string $inlinestyle = null): string {
		$rendered_html = '';

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

			$rendered_html .= '<div class="viewtable" style="' . $inlinestyle . '">';
			$rendered_html .= '<table>';

			if (isset($caption)) {
				$rendered_html .= '<caption>' . $caption . '</caption>';
			}

			if ($data_column_count > 1) {
				$rendered_html .= '<tr>';
				foreach ($ths as $th) {
					$rendered_html .= '<th>' . $th . '</th>';
				}
				$rendered_html .= '</tr>';

				foreach ($rows as $cells) {
					$rendered_html .= '<tr>';
					foreach ($cells as $cell) {
						$rendered_html .= '<td>' . $cell . '</td>';
					}
					$rendered_html .= '</tr>';
				}
			}
			else {
				$rendered_html .= '<tr><th>' . $ths . '</th></tr>';
				foreach ($rows as $cell) {
					$rendered_html .= '<tr><td>' . $cell . '</td></tr>';
				}
			}

			$rendered_html .= '</table>';
			$rendered_html .= '</div>';
		}
		else {
			$rendered_html .= 'Table column head count and minimum cell count in rows is differing.';
		}

		return $rendered_html;
	}

	public function prepare_mysql(job_db $job_db): bool {
		if (isset($job_db)) {

			$successchain = false;

			$directory = '../app/models/orms';
			$modelsorms_breadcrumb = '\models\orms';
			
			$modelorm_names = array();
			$modelorm_names_temp = $job_db->get_modelsorms_names($directory, $modelsorms_breadcrumb);
			foreach ($modelorm_names_temp as $modelorm_name_temp) {
				array_push($modelorm_names, str_replace('orm_', '', $modelorm_name_temp));
			}

			// Create modelorm tables.
			$this->prepare_mysql_add_tables($job_db, $modelorm_names, $directory);

			// Create modelorm foreignkeys.
			$successchain = $this->prepare_mysql_add_foreignkeys($job_db);

			if ($successchain) {
				// TODO: Put database-seeding script here.
			}

			return true;
		}
		return false;
	}

	public function prepare_mysql_add_tables(job_db $job_db, array $modelorm_names, string $directory): bool {
		if (isset($job_db) && isset($modelorm_names) && isset($directory)) {

			// Fetch tables exists in database.
			$tableindatabase_names = array();
			$result = $job_db->f3mysql_execute('SHOW TABLES');
			if (isset($result)) {
				foreach ($result as $table) {
					array_push($tableindatabase_names, $table['Tables_in_test_f3']);
				}
			}

			// Check if any modelorms are missing in database.
			$successchain = true;
			foreach ($modelorm_names as $modelorm_name) {
				$successchain = array_search($modelorm_name, $tableindatabase_names) !== false ? $successchain : false;
			}

			// Create tables (only if any missing).
			if (!$successchain) {
				if ($job_db->create_tables($directory)) {
					return true;
				}
			}

		}
		return false;
	}

	public function prepare_mysql_add_foreignkeys(job_db $job_db): bool {
		if (isset($job_db)) {

			$config_foreignkeys = [[
				'source_table' => 'order',
				'target_table' => 'product',
				'source_table_id' => 'fk_product_id',
				'target_table_id' => 'id',
				'ondelete' => 'CASCADE',
				'onupdate' => 'RESTRICT'
			],[
				'source_table' => 'order',
				'target_table' => 'customer',
				'source_table_id' => 'fk_customer_id',
				'target_table_id' => 'id',
				'ondelete' => 'CASCADE',
				'onupdate' => 'RESTRICT'
			]];

			foreach ($config_foreignkeys as $config_foreignkey) {
				$source_table = $config_foreignkey['source_table'];
				$target_table = $config_foreignkey['target_table'];
				$source_table_id = $config_foreignkey['source_table_id'];
				$target_table_id = $config_foreignkey['target_table_id'];
				$ondelete = $config_foreignkey['ondelete'];
				$onupdate = $config_foreignkey['onupdate'];
				$constraint_name = 'fk_' . $source_table . '_' . $target_table . '_' . $target_table_id;

				// TODO: Check constraint already exists.

				$mysql_statement = 'ALTER TABLE `' . $source_table . '`
				ADD CONSTRAINT `' . $constraint_name . '`  
				FOREIGN KEY ( `' . $source_table_id . '` ) REFERENCES `' . $target_table . '` ( `' . $target_table_id . '` )
				ON DELETE ' . $ondelete . ' ON UPDATE ' . $onupdate;

				//$result = $job_db->f3mysql_execute($mysql_statement);
			}

			return true;

		}
		return false;
	}
}