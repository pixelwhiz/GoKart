<?php

namespace pixelwhiz\minecart\utils;

use pixelwhiz\minecart\entity\Minecart;
use pixelwhiz\minecart\Minecarts;
use pocketmine\block\Air;
use pocketmine\block\Carpet;
use pocketmine\block\Lava;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\block\Water;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\SmokeParticle;
use pocketmine\world\sound\FizzSound;

class Controller {
    public static function shouldJump(Minecart $entity) : array {
        $currentX = $entity->getLocation()->x;
        $futureY = $entity->getLocation()->y + 0.25;
        $currentZ = $entity->getLocation()->z;

        $motion = ['x' => 0, 'y' => 0, 'z' => 0];

        $pos = new Vector3($entity->getLocation()->getFloorX(), $entity->getLocation()->getFloorY() - 0.25, $entity->getLocation()->getFloorZ());
        $block = $entity->getWorld()->getBlock($pos);


        if ((int)$entity->getEnergy() === 0) {
            $motion['y'] = 0;
            return $motion;
        }

        $floorY = $entity->getLocation()->getFloorY();
        $floorBlock = $entity->getWorld()->getBlockAt(0, $floorY, 0);
        if ($floorBlock instanceof Slab || $floorBlock instanceof Carpet || $block instanceof Air) {
            $motion['y'] = 0;
            return $motion;
        }

        if ($entity->getLocation()->getX() > 0) {
            for ($dx = -1; $dx <= 1; $dx++) {
                $futureX = $currentX + $dx;
                $blockInFrontX = $entity->getWorld()->getBlockAt((int)$futureX, (int)$futureY, (int)$currentZ);

                if (!$blockInFrontX->isTransparent()) {
                    $motion['y'] = 0.18;
                    break;
                }

                if ($blockInFrontX instanceof Carpet) {
                    $motion['y'] = 0.115;
                    break;
                }

                if ($blockInFrontX instanceof Slab) {
                    $motion['y'] = 0.15;
                    break;
                }

                if ($blockInFrontX instanceof Stair) {
                    $motion['y'] = 0.18;
                    break;
                }
            }
        } else if ($entity->getLocation()->getX() < 0) {
            for ($dx = -1.5; $dx <= -1; $dx++) {
                $futureX = $currentX + $dx;
                $blockInFrontX = $entity->getWorld()->getBlockAt((int)$futureX, (int)$futureY, (int)$currentZ);

                if (!$blockInFrontX->isTransparent()) {
                    $motion['y'] = 0.18;
                    break;
                }

                if ($blockInFrontX instanceof Carpet) {
                    $motion['y'] = 0.115;
                    break;
                }

                if ($blockInFrontX instanceof Slab) {
                    $motion['y'] = 0.15;
                    break;
                }

                if ($blockInFrontX instanceof Stair) {
                    $motion['y'] = 0.18;
                    break;
                }
            }
        }

        if ($entity->getLocation()->getZ() < 0) {
            for ($dz = -1.5; $dz <= -1.5; $dz++) {
                $futureZ = $currentZ + $dz;
                $blockInFrontZ = $entity->getWorld()->getBlockAt((int)$currentX, (int)$futureY, (int)$futureZ);

                if (!$blockInFrontZ->isTransparent()) {
                    $motion['y'] = 0.18;
                    break;
                }

                if ($blockInFrontZ instanceof Carpet) {
                    $motion['y'] = 0.115;
                    break;
                }

                if ($blockInFrontZ instanceof Slab) {
                    $motion['y'] = 0.15;
                    break;
                }

                if ($blockInFrontZ instanceof Stair) {
                    $motion['y'] = 0.18;
                    break;
                }
            }
        } elseif ($entity->getLocation()->getZ() > 0) {
            for ($dz = -1.5; $dz <= 1.5; $dz++) {
                $futureZ = $currentZ + $dz;
                $blockInFrontZ = $entity->getWorld()->getBlockAt((int)$currentX, (int)$futureY, (int)$futureZ);

                if (!$blockInFrontZ->isTransparent()) {
                    $motion['y'] = 0.18;
                    break;
                }

                if ($blockInFrontZ instanceof Carpet) {
                    $motion['y'] = 0.115;
                    break;
                }

                if ($blockInFrontZ instanceof Slab) {
                    $motion['y'] = 0.15;
                    break;
                }

                if ($blockInFrontZ instanceof Stair) {
                    $motion['y'] = 0.18;
                    break;
                }
            }
        }

        return $motion;
    }


    public static function shouldDrop(Minecart $entity) {
        $pos = $entity->getPosition();
        if ($entity->getWorld()->getBlockAt((int)$pos->getX(), (int)$pos->getY(), (int)$pos->getZ()) instanceof Water) {
            if ($player = Minecarts::getInstance()->getRider($entity)) {
                Minecarts::getInstance()->unride($player, $entity);
            }
        }
    }

    public static function shouldDespawn(Minecart $entity) {
        $pos = $entity->getPosition();
        if ($entity->getWorld()->getBlockAt((int)$pos->getX(), (int)$pos->getY(), (int)$pos->getZ()) instanceof Lava) {
            $entity->flagForDespawn();
            $entity->getWorld()->addSound($pos->asVector3(), new FizzSound(), $entity->getWorld()->getPlayers());
            $entity->getWorld()->addParticle($pos->asVector3(), new SmokeParticle(100), $entity->getWorld()->getPlayers());
        }
    }

    public static function getMotion(Minecart $entity) : array {
        $vector = [
            "x" => 0,
            "z" => 0,
        ];

        $player = $entity->getTargetEntity();
        if ($player instanceof Player) {
            $direction = $player->getDirectionVector();
            $energy = (int)$entity->getEnergy();

            // Adjust $vector based on energy levels
            if ($energy <= 100) {
                $vector = [
                    "x" => $direction->getX() / 1.5,
                    "z" => $direction->getZ() / 1.5,
                ];
            }
            if ($energy <= 80) {
                $vector = [
                    "x" => $direction->getX() / 1.75,
                    "z" => $direction->getZ() / 1.75,
                ];
            }
            if ($energy <= 60) {
                $vector = [
                    "x" => $direction->getX() / 2.0,
                    "z" => $direction->getZ() / 2.0,
                ];
            }
            if ($energy <= 40) {
                $vector = [
                    "x" => $direction->getX() / 2.25,
                    "z" => $direction->getZ() / 2.25,
                ];
            }
            if ($energy <= 20) {
                $vector = [
                    "x" => $direction->getX() / 2.50,
                    "z" => $direction->getZ() / 2.50,
                ];
            }
            if ($energy === 0) {
                $vector = [
                    "x" => 0,
                    "z" => 0,
                ];
            }
        }

        return $vector;
    }


    public static function updateFuel(Minecart $entity, array $startPos) : bool {
        if (is_null($startPos["x"]) || is_null($startPos["y"]) || is_null($startPos["z"])) {
            return false;
        }

        $startPosition = new Vector3($startPos["x"], $startPos["y"], $startPos["z"]);
        $currentPosition = $entity->getPosition();
        $distanceTravelled = $startPosition->distance($currentPosition);
        $entity->setEnergy($entity->getEnergy() - $distanceTravelled / 50000);

        return true;
    }


}