<?php

namespace CLADevs\VanillaX\blocks\utils;

use CLADevs\VanillaX\blocks\block\CommandBlock;
use InvalidArgumentException;
use pocketmine\block\BlockLegacyIds;
use pocketmine\utils\EnumTrait;

/**
 *
 * @method static CommandBlockType IMPULSE()
 * @method static CommandBlockType REPEAT()
 * @method static CommandBlockType CHAIN()
 */

final class CommandBlockType{
    use EnumTrait {
        __construct as Enum___construct;
    }

    const IMPULSE = 0;
    const REPEAT = 1;
    const CHAIN = 2;

    /** @var CommandBlockType[] */
    private static array $blockIdMap = [];
    /** @var CommandBlockType[] */
    private static array $modeIdMap = [];

    protected static function setup(): void{
        self::registerAll(
            new CommandBlockType("impulse", "Command Block", BlockLegacyIds::COMMAND_BLOCK, self::IMPULSE),
            new CommandBlockType("repeat", "Repeating Command Block", BlockLegacyIds::REPEATING_COMMAND_BLOCK, self::REPEAT),
            new CommandBlockType("chain", "Chain Command Block", BlockLegacyIds::CHAIN_COMMAND_BLOCK, self::CHAIN),
        );
    }

    protected static function register(CommandBlockType $member): void{
        self::_registryRegister($member->name(), $member);
        self::$blockIdMap[$member->getBlockId()] = $member;
        self::$modeIdMap[$member->getMode()] = $member;
    }

    public static function fromBlock(CommandBlock $block): self{
        self::checkInit();

        if(!isset(self::$blockIdMap[$id = $block->getId()])){
            throw new InvalidArgumentException("Unknown command block with id of $id");
        }
        return self::$blockIdMap[$id];
    }

    public static function fromMode(int $mode): self{
        self::checkInit();

        if(!isset(self::$modeIdMap[$mode])){
            throw new InvalidArgumentException("Unknown command block with mode of $mode");
        }
        return self::$modeIdMap[$mode];
    }

    private string $displayName;

    private int $blockId;
    private int $mode;

    private function __construct(string $enumName, string $displayName, int $blockId, int $mode){
        $this->Enum___construct($enumName);
        $this->displayName = $displayName;
        $this->blockId = $blockId;
        $this->mode = $mode;
    }

    public function isImpulse(): bool{
        return $this->blockId === BlockLegacyIds::COMMAND_BLOCK;
    }

    public function isRepeating(): bool{
        return $this->blockId === BlockLegacyIds::REPEATING_COMMAND_BLOCK;
    }

    public function isChain(): bool{
        return $this->blockId === BlockLegacyIds::CHAIN_COMMAND_BLOCK;
    }

    public function getDisplayName(): string{
        return $this->displayName;
    }

    public function getBlockId(): int{
        return $this->blockId;
    }

    public function getMode(): int{
        return $this->mode;
    }
}