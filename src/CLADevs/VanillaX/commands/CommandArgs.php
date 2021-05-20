<?php

namespace CLADevs\VanillaX\commands;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\CommandEnum;
use pocketmine\network\mcpe\protocol\types\CommandParameter;

class CommandArgs{

    /** @var CommandParameter[][] */
    private array $parameters = [];

    private int $flags;
    private int $permission;

    public function __construct(int $flags = 0, int $permission = 0){
        $this->flags = $flags;
        $this->permission = $permission;
    }

    public function getFlags(): int{
        return $this->flags;
    }

    public function getPermission(): int{
        return $this->permission;
    }

    public function addParameter(string $key, string $name, int $type = AvailableCommandsPacket::ARG_FLAG_VALID | AvailableCommandsPacket::ARG_TYPE_RAWTEXT, bool $isOptional = true): int{
        $param = new CommandParameter();
        $param->paramName = $name;
        $param->paramType = $type;
        $param->isOptional = $isOptional;
        $this->parameters[$key][] = $param;
        return count($this->parameters[$key]) - 1;
    }

    /**
     * @param string $key
     * @param int $id
     * @param string|null $name
     * @param string[] $values
     * @return bool
     */
    public function setEnum(string $key, int $id, ?string $name, array $values = []): bool{
        $parameter = $this->parameters[$key][$id] ?? null;

        if($parameter === null){
            return false;
        }
        if($name !== null){
            $enum = new CommandEnum();
            $enum->enumName = $name;
            $enum->enumValues = $values;
        }
        $parameter->enum = $name === null ? null : $enum;
        return true;
    }

    /**
     * @return CommandParameter[]
     */
    public function getOverload(): array{
        return $this->parameters;
    }
}