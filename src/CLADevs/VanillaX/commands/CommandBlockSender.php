<?php

namespace CLADevs\VanillaX\commands;

use pocketmine\command\RemoteConsoleCommandSender;

class CommandBlockSender extends RemoteConsoleCommandSender{


    public function getName(): string{
        return "Command Block";
    }
}