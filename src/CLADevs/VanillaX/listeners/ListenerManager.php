<?php

namespace CLADevs\VanillaX\listeners;

use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\Server;

class ListenerManager{

    public function startup(): void{
        Utils::callDirectory("listeners" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void{
            if(strpos($namespace, "PacketListener") !== false || strpos($namespace, "PlayerListener") !== false){
                $class = new $namespace($this);
            }else{
                $class = new $namespace();
            }
            Server::getInstance()->getPluginManager()->registerEvents($class, VanillaX::getInstance());
        });
    }
}