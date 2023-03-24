<?php

declare(strict_types=1);

namespace celis\async\mysql;

use Closure;

use celis\async\mysql\storage\Storage;

use celis\async\mysql\thread\MySQLTask;
use celis\async\mysql\thread\MySQLThreadPool;

use pocketmine\scheduler\ClosureTask;

use pocketmine\plugin\PluginBase;

/**
 * Class Loader
 * Creation date: 12.02.2023 11:36
 *
 * @package celis\async\mysql
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  celis <VK/Telegram/GitHub: @celishere, Email: celispost@icloud.com>
 */
class Loader extends PluginBase {

	private static MySQLThreadPool $threadPool;
	private static MySQLSettings $settings;

	public function onLoad(): void {
		$server = $this->getServer();

		self::$threadPool = new MySQLThreadPool(
			MySQLThreadPool::POOL_SIZE,
			MySQLThreadPool::MEMORY_LIMIT,
			$server->getLoader(),
			$server->getLogger(),
			$server->getTickSleeper()
		);

		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
			self::$threadPool->collectTasks();
		}), MySQLThreadPool::COLLECT_INTERVAL);
		$this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function (): void {
			self::$threadPool->triggerGarbageCollector();
		}), MySQLThreadPool::GARBAGE_COLLECT_INTERVAL);

		$this->saveResource("config.yml");
		$data = $this->getConfig()->getAll(true);

		self::$settings = new MySQLSettings(
			$data['host'] ?? "localhost",
			$data['port'] ?? 3306,
			$data['username'],
			$data['password'],
			$data['database']
		);

		Storage::getInstance();
	}

	/**
	 * @param string       $request
	 * @param Closure|null $closure
	 *
	 * @return void
	 */
	public static function addRequest(string $request, Closure $closure = null): void {
		self::$threadPool->submitTask(new MySQLTask([$request], self::$settings, $closure));
	}

	/**
	 * @param array         $requests
	 * @param Closure|null $closure
	 *
	 * @return void
	 */
	public static function addRequests(array $requests, Closure $closure = null): void {
		self::$threadPool->submitTask(new MySQLTask($requests, self::$settings, $closure));
	}
}