<?php

namespace pixelwhiz\minecart;

use pixelwhiz\minecart\entity\Minecart;
use pixelwhiz\minecart\items\AutoMinecart;
use pixelwhiz\minecart\utils\RandomUtils;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\SetActorLinkPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityLink;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\item\Minecart as PMMinecart;
use pocketmine\world\sound\PopSound;

class Minecarts {

    public static self $instance;

    public static array $isMoving = [];
    public static array $isRecharging = [];

    public static function init() : void {
        self::$instance = new self();
    }

    public static function getInstance() : self {
        if (!isset(self::$instance)) {
            self::init();
        }
        return self::$instance;
    }

    public function ride(Player $player, PMMinecart $item) {
        $nbt = RandomUtils::createBaseNBT($player->getPosition());
        $entity = new Minecart($player->getLocation(), $nbt);
        $entity->addMotion(0, 0.25, 0);
        $player->setTargetEntity($entity);
        $entity->setTargetEntity($player);
        $entity->spawnToAll();

        $namedTag = $item->getNamedTag();
        $energy = $namedTag->getTag("Energy") ? $namedTag->getFloat("Energy") : 100;
        $entity::$energy = $energy;

        self::$isMoving[$entity->getId()] = false;

        $player->getInventory()->setItemInHand(VanillaItems::AIR());

        $link = new SetActorLinkPacket();
        $link->link = new EntityLink($entity->getId(), $player->getId(), EntityLink::TYPE_RIDER, true, true);
        $player->getNetworkProperties()->setVector3(EntityMetadataProperties::RIDER_SEAT_POSITION, new Vector3(0, $entity->getSize()->getHeight() + 0.25, 0));
        $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::RIDING, true);
        $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::SADDLED, true);
        $player->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::WASD_CONTROLLED, true);
        NetworkBroadcastUtils::broadcastPackets($player->getWorld()->getPlayers(), [$link]);
    }

    public function unride(Player $player = null, Minecart $entity, bool $isClose = false) {
        if ($isClose === false) {
            $entity->flagForDespawn();
        } elseif ($isClose === true) {
            $entity->close();
        }

        unset(self::$isMoving[$entity->getId()]);

        $item = VanillaItems::MINECART();
        $entity->setTargetEntity(null);
        $player->setTargetEntity(null);
        $nbt = RandomUtils::setEnergyNBT($entity);
        $item->setNamedTag($nbt);
        if (AutoMinecart::isHoldAir($player)) {
            $player->getInventory()->setItemInHand($item);
        } else {
            $pos = $entity->getPosition()->asVector3();
            $entity->getWorld()->dropItem($pos, $item);
            $entity->getWorld()->addSound($pos, new PopSound(), $entity->getWorld()->getPlayers());
        }
    }

    public function isRiding(Player $player) : bool {
        $entity = $player->getTargetEntity();
        if (!$entity instanceof Minecart) return false;
        return true;
    }

    public function getMinecart(Player $player) : ?Minecart {
        $entity = $player->getTargetEntity();
        if (!$entity instanceof Minecart) return null;
        return $entity;
    }

    public function getRider(Minecart $entity) : ?Player {
        $player = $entity->getTargetEntity();
        if (!$player instanceof Player) return null;
        return $player;
    }

    public function isMoving(Minecart $entity) : bool {
        if (!isset(self::$isMoving[$entity->getId()]) || self::$isMoving[$entity->getId()] === false) {
            return false;
        }
        return true;
    }

    public function isRecharging(Minecart $entity) : bool {
        if (!isset(self::$isRecharging[$entity->getId()]) || self::$isRecharging[$entity->getId()] === false) {
            return false;
        }
        return true;
    }

}