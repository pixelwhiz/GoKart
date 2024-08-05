<?php

namespace pixelwhiz\minecart\entity;

use pixelwhiz\minecart\Minecarts;
use pixelwhiz\minecart\utils\Controller;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class Minecart extends Living {

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
        $this->location->pitch = $player->getLocation()->getPitch();

        Controller::shouldDrop($this);
        Controller::shouldDespawn($this);
        Controller::updateEnergy($this, self::$startPos);

        $motion = Controller::getMotion($this);
        $motionX = $motion["x"];
        $motionZ = $motion["z"];

        $this->move($motionX, 0, $motionZ);
        $this->addMotion(0, Controller::shouldJump($this)["y"], 0);

        return true;
    }

    public function walkBackward(): bool {
        $player = $this->getTargetEntity();
        if (!$player instanceof Player) return false;
        $this->location->yaw = $player->getLocation()->getYaw() + 90;
        $this->location->pitch = $player->getLocation()->getPitch();
        $direction = $player->getDirectionVector();

        Controller::shouldDrop($this);
        Controller::shouldDespawn($this);
        Controller::updateEnergy($this, self::$startPos);

        $motion = Controller::getMotion($this);
        $motionX = -$motion["x"] / 2;
        $motionZ = -$motion["z"] / 2;

        $this->move($motionX, 0, $motionZ);
        $this->addMotion(0, Controller::shouldJump($this)["y"], 0);


        return true;
    }

    protected function entityBaseTick(int $tickDiff = 1): bool
    {
        $player = Minecarts::getInstance()->getRider($this);
        if ($player instanceof Player) {
            if (Minecarts::getInstance()->isRecharging($this) === true) return false;
            $player->sendTip("Minecart Energy: ". (int)$this->getEnergy() . "%%");
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
        return "Minecart";
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
        return "minecraft:minecart";
    }

}