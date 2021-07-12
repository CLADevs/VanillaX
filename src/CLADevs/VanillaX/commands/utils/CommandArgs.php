<?php

namespace CLADevs\VanillaX\commands\utils;

use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\command\CommandEnum;
use pocketmine\network\mcpe\protocol\types\command\CommandParameter;
use pocketmine\network\mcpe\protocol\types\PlayerPermissions;

class CommandArgs{

    /** This flags can be used by $flags and these all flags are meant for the command name */

    /** White and Gray */
    const FLAG_NORMAL = 0;

    /** White and Aqua */
    const FLAG_AQUA = 1;

    /** @var CommandParameter[][] */
    private array $parameters = [];

    private int $flags;
    private int $permission;

    public function __construct(int $flags = self::FLAG_NORMAL, int $permission = PlayerPermissions::VISITOR){
        $this->flags = $flags;
        $this->permission = $permission;
    }

    public function getFlags(): int{
        return $this->flags;
    }

    public function getPermission(): int{
        return $this->permission;
    }

    /**
     * @param string $columnId , column id so you can use it in SetEnum function
     * @param string $name
     * @param int $type
     * @param bool $isOptional
     * @param string|null $enumName
     * @param array $enumValues
     * @param bool $customType
     * @param string|null $postfix
     * @return int, returns the key in the column for this parameter
     */
    public function addParameter(string $columnId, string $name, int $type = AvailableCommandsPacket::ARG_TYPE_RAWTEXT, bool $isOptional = true, string $enumName = null, array $enumValues = [], bool $customType = false, string $postfix = null): int{
        $param = new CommandParameter();
        $param->paramName = $name;
        $param->paramType = $customType? $type : AvailableCommandsPacket::ARG_FLAG_VALID | $type;
        $param->isOptional = $isOptional;
        $param->postfix = $postfix;
        $this->parameters[$columnId][] = $param;

        $columnKey = count($this->parameters[$columnId]) - 1;
        if($enumName !== null){
            $this->setEnum($columnId, $columnKey, $enumName, $enumValues);
        }
        return $columnKey;
    }

    /**
     * @param string $columnId
     * @param int $columnKey, this key can be returned after you've ran addParameter function
     * @param string|null $name
     * @param string[] $values
     * @return bool, if its true its successful, if not its not successful
     */
    public function setEnum(string $columnId, int $columnKey, ?string $name, array $values = []): bool{
        $parameter = $this->parameters[$columnId][$columnKey] ?? null;

        if($parameter === null){
            return false;
        }
        if($name !== null){
            $enum = new CommandEnum($name, $values);
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