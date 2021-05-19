<?php

namespace CLADevs\VanillaX\commands;

use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\Server;

class CommandManager{

    /** @var Command[] */
    private array $commands = [];

    public function startup(): void{
        Utils::callDirectory("commands" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void{
            /** @var Command $class */
            $class = new $namespace();
            if(!in_array(strtolower($class->getName()), VanillaX::getInstance()->getConfig()->getNested("disabled.commands", []))){
                Server::getInstance()->getCommandMap()->register("VanillaX", $class);
                $this->commands[strtolower($class->getName())] = $class;
            }
        });
    }

    /**
     * @return Command[]
     */
    public function getCommands(): array{
        return $this->commands;
    }

    public function getCommand(string $command): ?Command{
        return $this->commands[strtolower($command)] ?? null;
    }
}