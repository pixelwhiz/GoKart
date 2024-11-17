<?php

/*
 *    _____       _              _
 *   / ____|     | |            | |
 *  | |  __  ___ | | ____ _ _ __| |_
 *  | | |_ |/ _ \| |/ / _` | '__| __|
 *  | |__| | (_) |   < (_| | |  | |_
 *   \_____|\___/|_|\_\__,_|_|   \__|
 *
 * Copyright (C) 2024 pixelwhiz
 *
 * This software is distributed under "GNU General Public License v3.0".
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License v3.0
 * along with this program. If not, see <https://opensource.org/licenses/GPL-3.0>.
 */


namespace pixelwhiz\gokart\task;

use onebone\economyapi\EconomyAPI;
use pixelwhiz\gokart\entity\GokartEntity;
use pixelwhiz\gokart\Gokarts;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class RefillSchedulerTask extends Task {

    private GokartEntity $entity;
    private int $amount;
    private int $price;
    private int $currentEnergy;

    public function __construct(GokartEntity $entity, int $amount, int $price) {
        $this->entity = $entity;
        $this->amount = $amount;
        $this->price = $price;
        $this->currentEnergy = (int)$entity->getEnergy();
    }

    public function onRun(): void
    {
        $player = $this->entity->getTargetEntity();
        if (!$player instanceof Player) {
            $this->getHandler()->cancel();
            return;
        }
        $entity = $this->entity;
        $amount = $this->amount;
        $price = $this->price;
        Gokarts::$isRecharging[$entity->getId()] = true;

        if (Gokarts::getInstance()->isMoving($entity) === true) {
            $player->sendTitle(TextFormat::BOLD . TextFormat::RED ."You've Moved", TextFormat::YELLOW ."Failed to refill your minecart energy!", 20 * 5);
            unset(Gokarts::$isRecharging[$entity->getId()]);
            $this->getHandler()->cancel();
            return;
        }

        if ($this->currentEnergy < $amount) {
            $this->currentEnergy += 1;
            $player->sendTitle(TextFormat::BOLD . TextFormat::AQUA ."Don't Move", TextFormat::YELLOW ."Recharging minecart energy " . $this->currentEnergy . "%", 5, 5, 5);

            if ($this->currentEnergy > $amount) {
                $this->currentEnergy = $amount;
            }

            if ($this->currentEnergy === $amount) {
                $player->sendMessage(TextFormat::GRAY ."[Gas Station] ".TextFormat::GREEN."Successfully charged your minecart energy to {$amount}% for {$price} $");
                unset(Gokarts::$isRecharging[$entity->getId()]);
                $entity->setEnergy($amount);
                $economy = EconomyAPI::getInstance();
                $economy->reduceMoney($player, $price);
                $this->getHandler()->cancel();
            }

        }
    }
}
