<?php

namespace CLADevs\VanillaX\commands\sender;

use pocketmine\command\RemoteConsoleCommandSender;

class CommandBlockSender extends RemoteConsoleCommandSender{


    public function getName(): string{
        return "Command Block";
    }
}