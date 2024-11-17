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


namespace pixelwhiz\gokart\utils;

use pixelwhiz\gokart\Gokarts;
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

trait Controller {
    
    public function shouldJump() : array {
        $currentX = $this->getLocation()->x;
        $futureY = $this->getLocation()->y + 0.25;
        $currentZ = $this->getLocation()->z;

        $motion = ['x' => 0, 'y' => 0, 'z' => 0];

        $pos = new Vector3($this->getLocation()->getFloorX(), $this->getLocation()->getFloorY() - 0.25, $this->getLocation()->getFloorZ());
        $block = $this->getWorld()->getBlock($pos);

        if ((int)$this->getEnergy() === 0) {
            $motion['y'] = 0;
            return $motion;
        }

        $floorY = $this->getLocation()->getFloorY();
        $floorBlock = $this->getWorld()->getBlockAt(0, $floorY, 0);
        if ($floorBlock instanceof Slab || $floorBlock instanceof Carpet || $block instanceof Air) {
            $motion['y'] = 0;
            return $motion;
        }

        if ($this->getLocation()->getX() > 0) {
            for ($dx = -1; $dx <= 1; $dx++) {
                $futureX = $currentX + $dx;
                $blockInFrontX = $this->getWorld()->getBlockAt((int)$futureX, (int)$futureY, (int)$currentZ);

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
        } else if ($this->getLocation()->getX() < 0) {
            for ($dx = -1.5; $dx <= -1; $dx++) {
                $futureX = $currentX + $dx;
                $blockInFrontX = $this->getWorld()->getBlockAt((int)$futureX, (int)$futureY, (int)$currentZ);

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

        if ($this->getLocation()->getZ() < 0) {
            for ($dz = -1.5; $dz <= -1.5; $dz++) {
                $futureZ = $currentZ + $dz;
                $blockInFrontZ = $this->getWorld()->getBlockAt((int)$currentX, (int)$futureY, (int)$futureZ);

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
        } elseif ($this->getLocation()->getZ() > 0) {
            for ($dz = -1.5; $dz <= 1.5; $dz++) {
                $futureZ = $currentZ + $dz;
                $blockInFrontZ = $this->getWorld()->getBlockAt((int)$currentX, (int)$futureY, (int)$futureZ);

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


    public function shouldDrop() {
        $pos = $this->getPosition();
        if ($this->getWorld()->getBlockAt((int)$pos->getX(), (int)$pos->getY(), (int)$pos->getZ()) instanceof Water) {
            if ($player = Gokarts::getInstance()->getRider($this)) {
                Gokarts::getInstance()->unride($player, $this);
            }
        }
    }

    public function shouldDespawn() {
        $pos = $this->getPosition();
        if ($this->getWorld()->getBlockAt((int)$pos->getX(), (int)$pos->getY(), (int)$pos->getZ()) instanceof Lava) {
            $this->flagForDespawn();
            $this->getWorld()->addSound($pos->asVector3(), new FizzSound(), $this->getWorld()->getPlayers());
            $this->getWorld()->addParticle($pos->asVector3(), new SmokeParticle(100), $this->getWorld()->getPlayers());
        }
    }

    public function getGokartMotion(): Vector3 {
        $x = 0;
        $z = 0;

        $player = $this->getTargetEntity();
        if ($player instanceof Player) {
            $direction = $player->getDirectionVector();
            $energy = (int) $this->getEnergy();

            // Adjust $x and $z based on energy levels
            if ($energy <= 100) {
                $x = $direction->getX() / 1.5;
                $z = $direction->getZ() / 1.5;
            }
            if ($energy <= 80) {
                $x = $direction->getX() / 1.75;
                $z = $direction->getZ() / 1.75;
            }
            if ($energy <= 60) {
                $x = $direction->getX() / 2.0;
                $z = $direction->getZ() / 2.0;
            }
            if ($energy <= 40) {
                $x = $direction->getX() / 2.25;
                $z = $direction->getZ() / 2.25;
            }
            if ($energy <= 20) {
                $x = $direction->getX() / 2.50;
                $z = $direction->getZ() / 2.50;
            }
            if ($energy === 0) {
                $x = 0;
                $z = 0;
            }
        }

        return new Vector3($x, 0, $z);
    }

    public function updateEnergy(array $startPos) : bool {
        if (is_null($startPos["x"]) || is_null($startPos["y"]) || is_null($startPos["z"])) {
            return false;
        }

        $startPosition = new Vector3($startPos["x"], $startPos["y"], $startPos["z"]);
        $currentPosition = $this->getPosition();
        $distanceTravelled = $startPosition->distance($currentPosition);
        $this->setEnergy($this->getEnergy() - $distanceTravelled / 50000);

        return true;
    }


}

