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


namespace pixelwhiz\gokart\entity;

use pixelwhiz\gokart\Gokarts;
use pixelwhiz\gokart\utils\Controller;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;

class GokartEntity extends Living {

    use Controller;

    public static float $energy = 0;

    public static array $startPos = [
        "x" => null,
        "y" => null,
        "z" => null,
    ];

    public function getEnergy() : float {
        return self::$energy;
    }

    public function setEnergy(float $amount): bool {
        if ($amount <= 0) {
            self::$energy = 0;
            return false;
        }

        self::$energy = $amount;
        return true;
    }


    public function walk(): bool {
        $player = $this->getTargetEntity();
        if (!$player instanceof Player) return false;
        $this->location->yaw = $player->getLocation()->getYaw() + 90;

        $this->shouldDrop();
        $this->shouldDespawn();
        $this->updateEnergy(self::$startPos);

        $motion = $this->getGokartMotion();
        $motionX = $motion->getX();
        $motionZ = $motion->getZ();

        $this->move($motionX, 0, $motionZ);
        $this->addMotion(0, $this->shouldJump()["y"], 0);

        return true;
    }

    public function walkBackward(): bool {
        $player = $this->getTargetEntity();
        if (!$player instanceof Player) return false;
        $this->location->yaw = $player->getLocation()->getYaw() + 90;
        $this->location->pitch = $player->getLocation()->getPitch();
        $direction = $player->getDirectionVector();

        $this->shouldDrop();
        $this->shouldDespawn();
        $this->updateEnergy(self::$startPos);

        $motion = $this->getGokartMotion();
        $motionX = -$motion->getX() / 2;
        $motionZ = -$motion->getZ() / 2;

        $this->move($motionX, 0, $motionZ);
        $this->addMotion(0, $this->shouldJump($this)["y"], 0);

        return true;
    }

    protected function entityBaseTick(int $tickDiff = 1): bool
    {
        $player = Gokarts::getInstance()->getRider($this);
        if ($player instanceof Player) {
            if (Gokarts::getInstance()->isRecharging($this) === true) return false;
            $player->sendTip("Gokart Energy: ". (int)$this->getEnergy() . "%%");
            return true;
        }
        return false;
    }

    public function getOffsetPosition(Vector3 $vector3): Vector3
    {
        return $this->getPosition()->add(0, 0.25, 0);
    }

    public function getName(): string
    {
        return "GokartEntity";
    }

    public function getScale(): float
    {
        return 1.0;
    }

    public function getInitialDragMultiplier(): float {
        return 0;
    }

    protected function getInitialSizeInfo(): EntitySizeInfo
    {
        return new EntitySizeInfo(0.7, 0.98, 0.0);
    }

    protected function getInitialGravity(): float
    {
        return 0.1;
    }

    public static function getNetworkTypeId(): string
    {
        return EntityIds::MINECART;
    }

}