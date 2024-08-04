<?php

namespace pixelwhiz\minecart\listeners;

use pixelwhiz\minecart\entity\Minecart;
use pixelwhiz\minecart\items\AutoMinecart;
use pixelwhiz\minecart\Minecarts;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Minecart as MinecartItem;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\player\Player;

class MinecartListener implements Listener {

    public function onUse(PlayerItemUseEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($item instanceof MinecartItem) {
            if (Minecarts::getInstance()->isRiding($player) === true) return false;
            Minecarts::getInstance()->ride($player, $item);
            $event->cancel();
        }

        return true;
    }

    public function onReceive(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();
        if ($packet instanceof PlayerAuthInputPacket) {

            if (!Minecarts::getInstance()->isRiding($player)) {
                return false;
            }

            $moveVecX = $packet->getMoveVecX();
            $moveVecZ = $packet->getMoveVecZ();

            $isWPressed = $moveVecZ > 0.0;

            $isAPressed = $moveVecX < 0.0;
            $isSPressed = $moveVecZ < 0.0;
            $isDPressed = $moveVecX > 0.0;

            $entity = $player->getTargetEntity();
            if ($entity instanceof Minecart) {
                if ($isWPressed) {
                    if (count(array_filter($entity::$startPos, fn($value) => $value === null)) === 3) {
                        $entity::$startPos = [
                            "x" => $entity->getLocation()->getX(),
                            "y" => $entity->getLocation()->getY(),
                            "z" => $entity->getLocation()->getZ(),
                        ];
                    }

                    Minecarts::$isMoving[$entity->getId()] = true;
                    $entity->walk();
                } else if ($isSPressed) {
                    if (count(array_filter($entity::$startPos, fn($value) => $value === null)) === 3) {
                        $entity::$startPos = [
                            "x" => $entity->getLocation()->getX(),
                            "y" => $entity->getLocation()->getY(),
                            "z" => $entity->getLocation()->getZ(),
                        ];
                    }

                    Minecarts::$isMoving[$entity->getId()] = true;
                    $entity->walkBackward();
                } else if (!$isWPressed && !$isSPressed) {
                    Minecarts::$isMoving[$entity->getId()] = false;
                    $entity::$startPos = [
                        "x" => null,
                        "y" => null,
                        "z" => null,
                    ];
                }
            }
        }

        if ($packet instanceof InteractPacket) {
            if ($packet->action === InteractPacket::ACTION_LEAVE_VEHICLE && Minecarts::getInstance()->isRiding($player)) {
                Minecarts::getInstance()->unride($player, Minecarts::getInstance()->getMinecart($player));
            }
        }
        return true;
    }

    public function onDamage(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if (!$entity instanceof Minecart) return false;
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($damager instanceof Player) {
                Minecarts::getInstance()->unride($damager, $entity, true);
            }
        }
        $event->cancel();
        return true;
    }



    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        if (Minecarts::getInstance()->isRiding($player)) Minecarts::getInstance()->unride($player, Minecarts::getInstance()->getMinecart($player));
    }

}