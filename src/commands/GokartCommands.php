<?php

namespace pixelwhiz\minecart\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;

class GokartCommands extends Command {

    public function __construct()
    {
        parent::__construct("gokart", "Gokart commands", "/gokart spawn shopkeeper", [""]);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {

    }

}