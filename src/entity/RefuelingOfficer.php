<?php

namespace pixelwhiz\minecart\entity;

use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\network\mcpe\protocol\AddPlayerPacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class RefuelingOfficer extends Human {

    private const SKIN_URL = "https://t.novaskin.me/8b175679b95906e39c80505841e84ae17a49512ff636b36bd5ea3a1a539c1cd9";

    public function getSkin(): Skin
    {
        $skinData = $this->fetchSkinFromUrl(self::SKIN_URL);
        if ($skinData === false) {
            Server::getInstance()->getLogger()->error(TextFormat::RED . "Failed to fetch skin from URL.");
            return parent::getSkin(); // Fallback to default skin
        }
        return new Skin("RefuelingOfficerSkin", $skinData);
    }

    private function fetchSkinFromUrl(string $url): ?string
    {
        $imageData = @file_get_contents($url);
        if ($imageData === false) {
            return null;
        }

        // Assuming the image is in PNG format
        $image = @imagecreatefromstring($imageData);
        if ($image === false) {
            return null;
        }

        ob_start();
        imagepng($image);
        $pngData = ob_get_clean();
        imagedestroy($image);

        return $pngData;
    }
}
