<?php

declare(strict_types=1);

namespace pixelwhiz\minecart;

use pixelwhiz\minecart\entity\Minecart;
use pixelwhiz\minecart\listeners\GasStationListener;
use pixelwhiz\minecart\listeners\MinecartListener;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\event\Listener;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

use pocketmine\utils\Config;
use pocketmine\world\World;

class Main extends PluginBase implements Listener {

    public static self $instance;
    public Config $config;

    public static function getInstance() : self {
        return self::$instance;
    }

    public function onEnable(): void
    {
        parent::onEnable();
        self::$instance = $this;
        Minecarts::init();
        $this->config = new Config($this->getDataFolder() . "GasStation.json", Config::JSON);
        Server::getInstance()->getPluginManager()->registerEvents(new MinecartListener(), $this);
        Server::getInstance()->getPluginManager()->registerEvents(new GasStationListener(), $this);
        EntityFactory::getInstance()->register(Minecart::class, function (World $world, CompoundTag $nbt): Entity {
            return new Minecart(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["Minecart"]);
    }


    protected function onDisable(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $players) {
            if (Minecarts::getInstance()->isRiding($players)){
                if ($entity = $players->getTargetEntity() instanceof Minecart) {
                    Minecarts::getInstance()->unride($players,  Minecarts::getInstance()->getMinecart($players), true);
                }
            }
        }
    }

}
