<?php

namespace pixelwhiz\minecart\commands;

use pixelwhiz\minecart\Minecarts;
use pixelwhiz\minecart\utils\GasStation;
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
            $entity = Minecarts::getInstance()->getMinecart($sender);
            if ($entity === null) {
                $sender->sendMessage(TextFormat::RED."You must be in a minecart to use this command!");
                return false;
            }

            GasStation::open($sender, $entity);
        }
        return true;
    }


}