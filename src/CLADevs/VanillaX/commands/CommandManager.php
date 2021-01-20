<?php

namespace CLADevs\VanillaX\commands;

use pocketmine\Server;

class CommandManager{

    public function startup(): void{
        Server::getInstance()->getCommandMap()->register("Gamerule", new GameruleCommand());
    }
}