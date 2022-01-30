<?php

namespace CLADevs\VanillaX\commands\utils;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;

class CommandOverload{

    /** @var CommandParameter[] */
    private array $parameters = [];

    private function addParameter(CommandParameter $parameter): void{
        $this->parameters[] = $parameter;
    }

    public function addString(string $name, bool $optional = true, int $flags = 0): void{
        $this->addParameter(CommandParameter::standard($name, AvailableCommandsPacket::ARG_TYPE_STRING, $flags, $optional));
    }

    public function addFloat(string $name, bool $optional = true, int $flags = 0): void{
        $this->addParameter(CommandParameter::standard($name, AvailableCommandsPacket::ARG_TYPE_FLOAT, $flags, $optional));
    }

    public function addInt(string $name, bool $optional = true, int $flags = 0): void{
        $this->addParameter(CommandParameter::standard($name, AvailableCommandsPacket::ARG_TYPE_INT, $flags, $optional));
    }

    public function addTarget(string $name, bool $optional = true, int $flags = 0): void{
        $this->addParameter(CommandParameter::standard($name, AvailableCommandsPacket::ARG_TYPE_TARGET, $flags, $optional));
    }

    public function addPosition(string $name, bool $optional = true, int $flags = 0): void{
        $this->addParameter(CommandParameter::standard($name, AvailableCommandsPacket::ARG_TYPE_POSITION, $flags, $optional));
    }

    public function addJson(string $name, bool $optional = true, int $flags = 0): void{
        $this->addParameter(CommandParameter::standard($name, AvailableCommandsPacket::ARG_TYPE_JSON, $flags, $optional));
    }

    public function addEnum(string $name, CommandEnum $enum, bool $optional = true, int $flags = 0): void{
        $this->addParameter(CommandParameter::enum($name, $enum, $flags, $optional));
    }

    /**
     * @return CommandParameter[]
     */
    public function getParameters(): array{
        return $this->parameters;
    }
}