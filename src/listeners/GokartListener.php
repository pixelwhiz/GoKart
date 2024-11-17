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


namespace pixelwhiz\gokart\listeners;

use pixelwhiz\gokart\entity\GokartEntity;
use pixelwhiz\gokart\Gokarts;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\Minecart as MinecartItem;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\player\Player;

class GokartListener implements Listener {

    public function onUse(PlayerItemUseEvent $event) {
        $player = $event->getPlayer();
        $item = $event->getItem();
        if ($item instanceof MinecartItem) {
            if (Gokarts::getInstance()->isRiding($player) === true) return false;
            Gokarts::getInstance()->ride($player, $item);
            $event->cancel();
        }

        return true;
    }

    public function onReceive(DataPacketReceiveEvent $event) {
        $packet = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();
        if ($packet instanceof PlayerAuthInputPacket) {

            if (!Gokarts::getInstance()->isRiding($player)) {
                return false;
            }

            $moveVecX = $packet->getMoveVecX();
            $moveVecZ = $packet->getMoveVecZ();

            $isWPressed = $moveVecZ > 0.0;
            $isSPressed = $moveVecZ < 0.0;

            $entity = $player->getTargetEntity();
            if ($entity instanceof GokartEntity) {
                if ($isWPressed) {
                    if (count(array_filter($entity::$startPos, fn($value) => $value === null)) === 3) {
                        $entity::$startPos = [
                            "x" => $entity->getLocation()->getX(),
                            "y" => $entity->getLocation()->getY(),
                            "z" => $entity->getLocation()->getZ(),
                        ];
                    }

                    Gokarts::$isMoving[$entity->getId()] = true;
                    $entity->walk();
                } else if ($isSPressed) {
                    if (count(array_filter($entity::$startPos, fn($value) => $value === null)) === 3) {
                        $entity::$startPos = [
                            "x" => $entity->getLocation()->getX(),
                            "y" => $entity->getLocation()->getY(),
                            "z" => $entity->getLocation()->getZ(),
                        ];
                    }

                    Gokarts::$isMoving[$entity->getId()] = true;
                    $entity->walkBackward();
                } else if (!$isWPressed && !$isSPressed) {
                    Gokarts::$isMoving[$entity->getId()] = false;
                    $entity::$startPos = [
                        "x" => null,
                        "y" => null,
                        "z" => null,
                    ];
                }
            }
        }

        if ($packet instanceof InteractPacket) {
            if ($packet->action === InteractPacket::ACTION_LEAVE_VEHICLE && Gokarts::getInstance()->isRiding($player)) {
                Gokarts::getInstance()->unride($player, Gokarts::getInstance()->getMinecart($player));
            }
        }
        return true;
    }

    public function onDamage(EntityDamageEvent $event) {
        $entity = $event->getEntity();
        if ($entity instanceof GokartEntity) {
            if ($event instanceof EntityDamageByEntityEvent) {
                $damager = $event->getDamager();
                if ($damager instanceof Player) {
                    Gokarts::getInstance()->unride($damager, $entity, true);
                }
            }

            $event->cancel();
        }

        if ($entity instanceof Player) {
            if (Gokarts::getInstance()->isRiding($entity) and $event->getCause() === $event::CAUSE_FALL) {
                $event->cancel();
            }
        }

        return true;
    }



    public function onQuit(PlayerQuitEvent $event) {
        $player = $event->getPlayer();
        if (Gokarts::getInstance()->isRiding($player)) Gokarts::getInstance()->unride($player, Gokarts::getInstance()->getMinecart($player));
    }

}