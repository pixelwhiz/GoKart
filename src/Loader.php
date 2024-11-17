<?php

declare(strict_types=1);


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

namespace pixelwhiz\gokart;

use pixelwhiz\gokart\commands\GokartCommands;
use pixelwhiz\gokart\entity\GokartEntity;
use pixelwhiz\gokart\listeners\GokartListener;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

use pocketmine\utils\Config;
use pocketmine\world\World;

class Loader extends PluginBase {

    public static self $instance;
    public Config $config;

    public static function getInstance() : self {
        return self::$instance;
    }

    public function onEnable(): void
    {
        parent::onEnable();
        self::$instance = $this;
        Gokarts::init();
        Server::getInstance()->getCommandMap()->register("gokart", new GokartCommands());
        Server::getInstance()->getPluginManager()->registerEvents(new GokartListener(), $this);
        EntityFactory::getInstance()->register(GokartEntity::class, function (World $world, CompoundTag $nbt): Entity {
            return new GokartEntity(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ["GokartEntity"]);
    }


    protected function onDisable(): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $players) {
            if (Gokarts::getInstance()->isRiding($players)){
                if ($entity = $players->getTargetEntity() instanceof GokartEntity) {
                    Gokarts::getInstance()->unride($players,  Gokarts::getInstance()->getMinecart($players), true);
                }
            }
        }
    }

}
