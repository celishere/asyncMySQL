<?php

declare(strict_types=1);

namespace celis\async\mysql;

/**
 * Class MySQLResponse
 * Creation date: 12.02.2023 11:46
 *
 * @package celis\async\mysql
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  celis <VK/Telegram/GitHub: @celishere, Email: celispost@icloud.com>
 */
readonly class MySQLResponse {

	/**
	 * @param array $responses
	 */
	public function __construct(private array $responses) {}

	/**
	 * @return array
	 */
	public function getResponses(): array {
		return $this->responses;
	}

	/**
	 * @return array
	 */
	public function getResponse(): array {
		return $this->responses[0];
	}
}