<?php

namespace pixelwhiz\minecart\listeners;

use pixelwhiz\minecart\forms\EnergyShop;
use pixelwhiz\minecart\Main;
use pixelwhiz\minecart\Minecarts;
use pocketmine\block\Wool;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class GasStationListener implements Listener {

    public function onInteract(PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $block = $event->getBlock();

        $x = $block->getPosition()->getX();
        $y = $block->getPosition()->getY();
        $z = $block->getPosition()->getZ();
        $world = $block->getPosition()->getWorld();

        $config = Main::getInstance()->config;
//         foreach ($config as $blockData) {
//            if ($x === $blockData["x"] &&
//                $y === $blockData["y"] &&
//                $z === $blockData["z"] &&
//                $world->getFolderName() === $blockData["world"]) {
//                if (Minecarts::getInstance()->isRiding($player)) {
//                    EnergyShop::open($player, Minecarts::getInstance()->getMinecart($player));
//                }
//            }
//        }

        if ($block instanceof Wool) {
            if (Minecarts::getInstance()->isRiding($player)) {
                EnergyShop::open($player, Minecarts::getInstance()->getMinecart($player));
            }
        }


    }

}