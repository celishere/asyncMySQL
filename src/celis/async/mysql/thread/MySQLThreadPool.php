<?php

declare(strict_types=1);

namespace celis\async\mysql\thread;

use pocketmine\scheduler\AsyncPool;
use pocketmine\scheduler\DumpWorkerMemoryTask;
use pocketmine\scheduler\GarbageCollectionTask;

/**
 * Class MySQLThreadPool
 * Creation date: 12.02.2023 11:37
 *
 * @package celis\async\mysql\thread
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  celis <VK/Telegram/GitHub: @celishere, Email: celispost@icloud.com>
 */
class MySQLThreadPool extends AsyncPool {

	public const MEMORY_LIMIT = 256; // 256MB Limit
	public const POOL_SIZE = 2; // 2 workers
	public const COLLECT_INTERVAL = 1; // 1 tick
	public const GARBAGE_COLLECT_INTERVAL = 15 * 60 * 20; // 15 min

	/**
	 * @param string $outputFolder
	 * @param int $maxNesting
	 * @param int $maxStringSize
	 * @return void
	 */
	public function dumpMemory(string $outputFolder, int $maxNesting, int $maxStringSize): void {
		foreach ($this->getRunningWorkers() as $i) {
			$this->submitTaskToWorker(new DumpWorkerMemoryTask($outputFolder, $maxNesting, $maxStringSize), $i);
		}
	}

	/**
	 * @return int
	 */
	public function triggerGarbageCollector(): int {
		$this->shutdownUnusedWorkers();

		foreach ($this->getRunningWorkers() as $i) {
			$this->submitTaskToWorker(new GarbageCollectionTask(), $i);
		}

		return gc_collect_cycles();
	}
}