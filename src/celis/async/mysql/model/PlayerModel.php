<?php

namespace celis\async\mysql\model;

use pocketmine\player\Player;

/**
 * Class PlayerModel
 * Creation date: 12.02.2023 12:00
 *
 * @package celis\async\mysql\model
 *
 * @version 2.0.0
 * @since   1.0.0
 *
 * @author  celis <VK/Telegram/GitHub: @celishere, Email: celispost@icloud.com>
 *
 * @property string $username
 */
class PlayerModel extends Model {

	protected array $ignoreFields = ['username'];

	/**
	 * @param Player $player
	 *
	 * @return void
	 */
	public function setPlayer(Player $player): void {
		$this->username = strtolower($player->getName());
	}
}