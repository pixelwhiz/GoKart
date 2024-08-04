<?php

namespace pixelwhiz\minecart\utils;

use pixelwhiz\minecart\Main;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class GasStation {

    public static function saveBlockPosition(int $x, int $y, int $z, string $world): void {
        $config = Main::getInstance()->config;
        $blocks = $config->get("blocks", []);
        $blocks[] = [
            "x" => $x,
            "y" => $y,
            "z" => $z,
            "world" => $world,
        ];
        $config->set("blocks", $blocks);
        $config->save();
    }

}