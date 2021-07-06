<?php

namespace CLADevs\VanillaX\commands;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\Server;

class CommandManager{

    /** @var Command[] */
    private array $commands = [];

    public function startup(): void{
        Utils::callDirectory("commands" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void{
            if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                $this->register(new $namespace());
            }
        });
    }

    public function register(Command $command): void{
        if(in_array(strtolower($command->getName()), VanillaX::getInstance()->getConfig()->getNested("disabled.commands", [])) || !$command->canRegister()){
            return;
        }
        $map = Server::getInstance()->getCommandMap();
        if(($cmd = $map->getCommand($command->getName())) !== null){
            $map->unregister($cmd);
        }
       $map->register("VanillaX", $command);
        $this->commands[strtolower($command->getName())] = $command;
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