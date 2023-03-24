<?php

declare(strict_types=1);

namespace celis\async\mysql;

/**
 * Class MySQLSettings
 * Creation date: 12.02.2023 12:09
 *
 * @package celis\async\mysql
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  celis <VK/Telegram/GitHub: @celishere, Email: celispost@icloud.com>
 */
readonly class MySQLSettings {

	/**
	 * @param string $host
	 * @param int    $port
	 * @param string $username
	 * @param string $password
	 * @param string $database
	 */
	public function __construct(
		private string $host,
		private int    $port,
		private string $username,
		private string $password,
		private string $database) {
	}

	/**
	 * @return string
	 */
	public function getHost(): string
	{
		return $this->host;
	}

	/**
	 * @return int
	 */
	public function getPort(): int
	{
		return $this->port;
	}

	/**
	 * @return string
	 */
	public function getUsername(): string
	{
		return $this->username;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @return string
	 */
	public function getDatabase(): string
	{
		return $this->database;
	}
}