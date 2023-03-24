<?php

declare(strict_types=1);

namespace celis\async\mysql\utils;

use mysqli_result;

/**
 * Class Utils
 * Creation date: 12.02.2023 12:02
 *
 * @package celis\async\mysql\utils
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  celis <VK/Telegram/GitHub: @celishere, Email: celispost@icloud.com>
 */
class MySQLUtils {

	/**
	 * @param $val
	 *
	 * @return string
	 */
	public static function getVarType($val): string {
		if (is_double($val)) {
			return 'd';
		}

		if (is_int($val)) {
			return 'i';
		}

		if (is_string($val)) {
			return 's';
		}

		return 'b';
	}

	/**
	 * @param mysqli_result $mysqli_result
	 * @param array          $data
	 *
	 * @return array
	 */
	public static function correctFields(mysqli_result $mysqli_result, array $data): array {
		$i = 0;

		$fields = json_decode(json_encode(mysqli_fetch_fields($mysqli_result)), true);

		foreach ($data as $key => $result) {
			$field = $fields[$i]['type'];

			$data[$key] = match ($field) {
				3 => (int) $result,
				4 => (float) $result,
				6 => null,
				default => (string) $result
			};

			$i++;
		}

		return $data;
	}
}