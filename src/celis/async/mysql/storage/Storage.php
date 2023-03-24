<?php

declare(strict_types=1);

namespace celis\async\mysql\storage;

use celis\async\mysql\model\Model;

use pocketmine\utils\SingletonTrait;

/**
 * Class Storage
 * Creation date: 23.03.2023 23:20
 *
 * @package celis\async\mysql\storage
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  celis <VK/Telegram/GitHub: @celishere, Email: celispost@icloud.com>
 */
class Storage {
	use SingletonTrait;

	/** @var Model[] */
	private array $models = [];

	/**
	 * @param Model $model
	 *
	 * @return int
	 */
	public function addModel(Model $model): int {
		$this->models[] = $model;

		return count($this->models);
	}

	/**
	 * @param int $id
	 *
	 * @return Model|null
	 */
	public function getModel(int $id): ?Model {
		return $this->models[$id];
	}

	/**
	 * @param int $id
	 *
	 * @return void
	 */
	public function removeModel(int $id): void {
		unset($this->models[$id]);
	}
}