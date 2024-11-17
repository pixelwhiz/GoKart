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

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\ModalForm;
use onebone\economyapi\EconomyAPI;
use pixelwhiz\gokart\entity\GokartEntity;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class GasStation {

    private static int $tax = 2500;
    private static int $price = 7650 / 2;

    public static function open(Player $player, GokartEntity $entity) : CustomForm {
        $minEnergy = (int)$entity->getEnergy();
        $maxEnergy = 100;
        $stepSlider = [];
        for ($i = $minEnergy; $i <= $maxEnergy; $i++) {
            $stepSlider[] = (string)$i;
        }

        $form = new CustomForm(function (Player $player, $data) use ($entity, $stepSlider) {
            if ($data === null || !isset($data[1])) {
                return false;
            }

            $index = (int)$data[1];
            if ($index < 0 || $index >= count($stepSlider)) {
                return false;
            }

            $amount = (int)$stepSlider[$index];
            if ($amount === (int)$entity->getEnergy()) {
                $player->sendMessage(TextFormat::GRAY."[Gas Station] ".TextFormat::YELLOW."No change in energy level.");
                return false;
            }

            $energy = (int)$entity->getEnergy();
            self::confirm($player, $energy, $amount);
            return true;
        });

        $form->setTitle("Gas Station");
        $form->addLabel("GokartEntity ID: #".$entity->getId()."\nEnergy: ". (int)$entity->getEnergy() . "%%\n");
        $form->addStepSlider("Select Amount", $stepSlider);
        $form->sendToPlayer($player);
        return $form;
    }

    public static function confirm(Player $player, int $energy, int $amount) : ModalForm {
        $form = new ModalForm(function (Player $player, bool $data = false) use ($energy, $amount) {
            if (!is_bool($data)) {
                self::confirm($player, $energy, $amount);
                return false;
            }
            if ($data === true) {
                $economy = EconomyAPI::getInstance();
                $money = $economy->myMoney($player);
                $price = ($amount - $energy) * self::$price + self::$tax;
                if ($money >= $price) {
                    $entity = $player->getTargetEntity();
                    if ($entity instanceof GokartEntity) {
                        Controller::refillEnergy($entity, $amount, $price);
                    } else {
                        $player->sendMessage(TextFormat::GRAY."[Gas Station]".TextFormat::YELLOW ."Failed to refill please use your minecart!");
                    }
                    return false;
                } else {
                    $priceNeeded = $price - $money;
                    $player->sendMessage(TextFormat::GRAY."[Gas Station] ".TextFormat::YELLOW."You need another {$priceNeeded} $ to buy {$amount}% energy!");
                    return false;
                }
            }
            return true;
        });


        $form->setTitle("Gas Station");
        $price = ($amount - $energy) * self::$price;
        $tax = self::$tax;
        $total = number_format($price + $tax);
        $form->setContent(
            "Price: ".number_format($price)."\n" .
            "Tax: ".number_format($tax)."\n\n" .
            "Total: {$total} $\n\n" .
            "Click confirm button to purchase it."
        );
        $form->setButton1(TextFormat::GREEN ."Confirm");
        $form->setButton2("Cancel");
        $form->sendToPlayer($player);
        return $form;
    }

}