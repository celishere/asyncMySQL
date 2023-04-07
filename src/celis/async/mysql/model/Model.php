<?php

declare(strict_types=1);

namespace celis\async\mysql\model;

use celis\async\mysql\Loader;
use celis\async\mysql\MySQLResponse;

/**
 * Class Model
 * Creation date: 12.02.2023 12:00
 *
 * @package celis\async\mysql\model
 *
 * @version 1.0.0
 * @since   1.0.0
 *
 * @author  celis <VK/Telegram/GitHub: @celishere, Email: celispost@icloud.com>
 */
class Model {

	public static string $table;

	/**
	 * @var string[]
	 */
	protected array $fillable = [];
	protected array $ignoreFields = [];

	protected array $data = [];

	protected array $dirtyData = [];

	protected bool $isDirty = false;

	public bool $created = false;

	public int $id = -1;

	/**
	 * @return string
	 */
	public static function getTable(): string{
		return static::$table;
	}

	public function fetch(): void {
		Loader::addRequest(
			sprintf("SELECT * from `%s` where username = '%s'", static::$table, $this->data['username']),
			function (MySQLResponse $mySQLResponse): void {
				$response = $mySQLResponse->getResponse();
	 			if (empty($response)) {
					return;
				}

				$keys_to_keep = array_diff_key($response, array_flip($this->ignoreFields));
				$response = array_intersect_key($response, $keys_to_keep);

				foreach ($response as $key => $value) {
					$this->{$key} = $value;
				}

				$this->created = true;
			}
		);
	}

	/**
	 * @param $name
	 *
	 * @return mixed
	 */
	public function __get($name) {
		if (!isset($this->data[$name])) {
			return null;
		}

		return $this->data[$name];
	}

	/**
	 * @param $name
	 * @param $value
	 */
	public function __set($name, $value): void {
		if (in_array($name, $this->fillable) && (!isset($this->data[$name]) || $this->data[$name] !== $value)) {
			if ($this->created) {
				$this->isDirty = true;
				$this->dirtyData[$name] = $value;
			}
		}

		$this->data[$name] = $value;
	}

	/**
	 * @return true
	 */
	public function save(): bool {
		if (!$this->isDirty || count($this->dirtyData) == 0) {
			return true;
		}

		$this->created ? $this->update() : $this->create();
		return true;
	}

	/**
	 * @return array
	 */
	public function getDirtyData(): array{
		$this->isDirty = false;

		$dirtyData = $this->dirtyData;
		$this->dirtyData = [];

		return $dirtyData;
	}

	/**
	 * @return array
	 */
	public function toArray(): array{
		return $this->data;
	}

	public function update(): void {
		$data = $this->getDirtyData();

		$values = [];

		foreach ($data as $key => $value) {
			if (is_string($value)) {
				$value = "'$value'";
			}

			$values[] = $key . " = " . $value;
		}

		$query = sprintf('UPDATE `%s` SET %s WHERE `id` = %d', self::getTable(), join(', ', $values), $this->id);

		Loader::addRequest($query);
	}

	/**
	 * @return void
	 */
	public function create(): void {
		$data = $this->toArray();

		$columns = [];
		$values = [];

		foreach ($data as $key => $value) {
			if (is_string($value)) {
				$value = "'$value'";
			}

			$columns[] = $key;
			$values[] = $value;
		}

		$query = sprintf('INSERT INTO `%s` (%s) VALUES (%s)', self::getTable(), join(', ', $columns), join(', ', $values));

		Loader::addRequest($query);
	}
}
