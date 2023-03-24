<?php

declare(strict_types=1);

namespace celis\async\mysql\thread;

use Closure;

use Exception;
use InvalidArgumentException;

use mysqli;

use celis\async\mysql\MySQLResponse;
use celis\async\mysql\MySQLSettings;

use celis\async\mysql\utils\MySQLUtils;

use pocketmine\scheduler\AsyncTask;

use pocketmine\utils\Utils;

/**
 * Class MySQLTask
 * Creation date: 12.02.2023 11:44
 *
 * @package celis\async\mysql\thread
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  celis <VK/Telegram/GitHub: @celishere, Email: celispost@icloud.com>
 */
class MySQLTask extends AsyncTask {

	protected array $requests;
	protected MySQLSettings $settings;

	/**
	 * @param string[]         $requests
	 * @param Closure|null $closure
	 */
	public function __construct(array $requests, MySQLSettings $settings, Closure $closure = null) {
		$this->requests = $requests;
		$this->settings = $settings;

		if ($closure !== null) {
			Utils::validateCallableSignature(function (MySQLResponse $result): void {}, $closure);
			$this->storeLocal('closure', $closure);
		}
	}

	/**
	 * @throws Exception
	 */
	protected function connect(): mysqli {
		$settings = $this->settings;

		$connection = new mysqli($settings->getHost(), $settings->getUsername(), $settings->getPassword(), $settings->getDatabase(), $settings->getPort());
		if ($connection->connect_error) {
			throw new Exception('Error connect to db %s: %s', $connection->connect_errno, $connection->connect_error);
		}

		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

		return $connection;
	}

	public function onRun(): void {
		$responses = [];

		try {
			$connection = $this->connect();
		} catch (Exception) {
			return;
		}

		foreach ($this->requests as $key => $query) {
			$stmt = $connection->query($query);

			if (!is_bool($stmt)) { //нет ответа
				if (is_array($response = $stmt->fetch_assoc())) {
					$responses[$key] = MySQLUtils::correctFields($stmt, $response);
				}
			}
		}

		$this->setResult($responses);
	}

	public function onCompletion(): void {
		try {
			/** @var Closure $closure */
			$closure = $this->fetchLocal('closure');
		} catch (InvalidArgumentException) {
			return;
		}

		if ($closure !== null) {
			$closure(new MySQLResponse($this->getResult()));
		}
	}
}