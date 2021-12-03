<?php

namespace CLADevs\VanillaX\listeners;

use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\Server;

class ListenerManager{

    public function startup(): void{
        Utils::callDirectory("listeners" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void{
            Server::getInstance()->getPluginManager()->registerEvents(new $namespace(), VanillaX::getInstance());
        });
    }
}