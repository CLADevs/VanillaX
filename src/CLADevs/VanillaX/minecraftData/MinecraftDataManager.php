<?php

namespace CLADevs\VanillaX\minecraftData;

use CLADevs\VanillaX\scheduler\DownloadMinecraftDataTask;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class MinecraftDataManager{

    public function startup(): void{
        $path = Utils::getMinecraftDataPath();

        if(!file_exists($path)){
            @mkdir($path);
            VanillaX::getInstance()->getLogger()->notice(TextFormat::RED . "Minecraft data not found, downloading..");
            Server::getInstance()->getAsyncPool()->submitTask(new DownloadMinecraftDataTask($path));
            return;
        }
    }
}