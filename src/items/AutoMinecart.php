<?php

namespace pixelwhiz\minecart\items;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class AutoMinecart {

    public static function isHoldAir(Player $player) : bool {
        $hand = $player->getInventory()->getItemInHand();
        if ($hand->getTypeId() !== VanillaItems::AIR()->getTypeId()) {
            return false;
        }
        return true;
    }

}