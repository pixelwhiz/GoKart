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


namespace pixelwhiz\gokart\commands;

use pixelwhiz\gokart\Gokarts;
use pixelwhiz\gokart\utils\GasStation;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class GokartCommands extends Command {

    public function __construct()
    {
        parent::__construct("gokart", "Recharge your minecart energy", TextFormat::GRAY."Usage: ".TextFormat::RED."/gokart recharge", [""]);
        $this->setPermission("gokart.cmd");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) return false;

        if (!isset($args[0])) {
            $sender->sendMessage($this->getUsage());
            return false;
        }
        
        if ($args[0] === "recharge") {
            $entity = Gokarts::getInstance()->getMinecart($sender);
            if ($entity === null) {
                $sender->sendMessage(TextFormat::RED."You must be in a minecart to use this command!");
                return false;
            }

            GasStation::open($sender, $entity);
        }
        return true;
    }


}