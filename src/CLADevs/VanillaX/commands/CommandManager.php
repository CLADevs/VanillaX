<?php

namespace CLADevs\VanillaX\commands;

use CLADevs\VanillaX\utils\Utils;
use pocketmine\Server;

class CommandManager{

    public function startup(): void{
        Utils::callDirectory("commands" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void{
            Server::getInstance()->getCommandMap()->register("VanillaX", new $namespace());
        });
    }
}