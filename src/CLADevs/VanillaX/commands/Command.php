<?php

namespace CLADevs\VanillaX\commands;

abstract class Command extends \pocketmine\command\Command{

    protected ?CommandArgs $commandArg = null;

    public function getCommandArg(): ?CommandArgs{
        return $this->commandArg;
    }
}