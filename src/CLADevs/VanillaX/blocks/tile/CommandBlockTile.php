<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\block\CommandBlock;
use CLADevs\VanillaX\blocks\TileIds;
use CLADevs\VanillaX\blocks\utils\CommandBlockType;
use Exception;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\NameableTrait;
use pocketmine\block\tile\Spawnable;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class CommandBlockTile extends Spawnable implements Nameable{
    use NameableTrait;

    const TAG_COMMAND = "Command";
    const TAG_EXECUTE_ON_FIRST_TICK = "ExecuteOnFirstTick";
    const TAG_LOOP_COMMAND_MODE = "LPCommandMode";
    const TAG_LOOP_CONDITIONAL_MODE = "LPCondionalMode";
    const TAG_LOOP_REDSTONE_MODE = "LPRedstoneMode";
    const TAG_LAST_EXECUTION = "LastExecution";
    const TAG_LAST_OUTPUT = "LastOutput";
    const TAG_LAST_OUTPUT_PARAMS = "LastOutputParams";
    const TAG_SUCCESS_COUNT = "SuccessCount";
    const TAG_TICK_DELAY = "TickDelay";
    const TAG_TRACK_OUTPUT = "TrackOutput";
    const TAG_AUTO = "auto";
    const TAG_CONDITION_MET = "conditionMet";
    const TAG_CONDITIONAL_MODE = "conditionalMode";
    const TAG_RAN_COMMAND = "hasRanCommand";

    const TILE_ID = TileIds::COMMAND_BLOCK;
    const TILE_BLOCK = [BlockLegacyIds::COMMAND_BLOCK, BlockLegacyIds::CHAIN_COMMAND_BLOCK, BlockLegacyIds::REPEATING_COMMAND_BLOCK];

    private string $command = "";
    private string $lastOutput = "";

    private int $tickDelay = 0;

    private bool $isRedstoneMode = true;
    private bool $isConditional = false;
    private bool $executeOnFirstTick = false;
    private bool $shouldTrackOutput = true;
    private bool $ranCommand = false;

    /**
     * @throws Exception
     */
    public function handleCommandBlockUpdate(Player $player, CommandBlockUpdatePacket $packet): void{
        if($packet->isBlock && $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $this->customName = $packet->name;
            $this->lastOutput = $packet->lastOutput;
            $this->tickDelay = $packet->tickDelay;
            $this->isConditional = $packet->isConditional;
            $this->executeOnFirstTick = $packet->executeOnFirstTick;
            $this->shouldTrackOutput = $packet->shouldTrackOutput;

            if(($value = $packet->command) !== $this->command){
                $this->command = $value;
            }
            if(($value = $packet->isRedstoneMode) !== $this->isRedstoneMode){
                $this->isRedstoneMode = $value;
                if($value) $this->clearOldRanCommands();
            }
            $newType = CommandBlockType::fromMode($packet->commandBlockMode);
            if(!$newType->equals($this->getType())){
                $this->getPosition()->getWorld()->setBlock($this->getPosition(), BlockFactory::getInstance()->get($newType->getBlockId(), $this->getBlock()->getMeta()));
                $this->clearOldRanCommands();
            }
            $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1);
            $this->setDirty();
            $this->position->getWorld()->setBlock($this->position, $this->getBlock());
        }
    }

    public function readSaveData(CompoundTag $nbt): void{
        $this->loadName($nbt);

        foreach($nbt->getValue() as $name => $tag){
            $value = $tag->getValue();

            switch($name){
                case self::TAG_COMMAND:
                    $this->command = $value;
                    break;
                case self::TAG_LAST_OUTPUT:
                    $this->lastOutput = $value;
                    break;
                case self::TAG_TICK_DELAY:
                    $this->tickDelay = $value;
                    break;
                case self::TAG_AUTO:
                    $this->isRedstoneMode = (bool)$value;
                    break;
                case self::TAG_CONDITIONAL_MODE:
                    $this->isConditional = (bool)$value;
                    break;
                case self::TAG_EXECUTE_ON_FIRST_TICK:
                    $this->executeOnFirstTick = (bool)$value;
                    break;
                case self::TAG_TRACK_OUTPUT:
                    $this->shouldTrackOutput = (bool)$value;
                    break;
                case self::TAG_RAN_COMMAND:
                    $this->ranCommand = (bool)$value;
                    break;
            }
        }
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->addAdditionalSpawnData($nbt);
        $nbt->setByte(self::TAG_RAN_COMMAND, $this->ranCommand);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        $this->saveName($nbt);
        $nbt->setString(self::TAG_COMMAND, $this->command);
        $nbt->setString(self::TAG_LAST_OUTPUT, $this->lastOutput);
        $nbt->setInt(self::TAG_TICK_DELAY, $this->tickDelay);
        $nbt->setByte(self::TAG_AUTO, !$this->isRedstoneMode);
        $nbt->setByte(self::TAG_CONDITIONAL_MODE, $this->isConditional);
        $nbt->setByte(self::TAG_EXECUTE_ON_FIRST_TICK, $this->executeOnFirstTick);
        $nbt->setByte(self::TAG_TRACK_OUTPUT, $this->shouldTrackOutput);
    }

    /**
     * @throws Exception
     */
    public function clearOldRanCommands(): void{
        $this->setRanCommand(false);
        $this->getCommandBlock()->clearTickDelay();
    }

    public function setRanCommand(bool $ranCommand): void{
        $this->ranCommand = $ranCommand;
    }

    public function setLastOutput(string $lastOutput): void{
        $this->lastOutput = $lastOutput;
    }

    public function getTickDelay(): int{
        return $this->tickDelay;
    }

    public function getCommand(): string{
        return $this->command;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function canRun(): bool{
        //TODO chain
        switch($this->getType()->name()){
            case CommandBlockType::IMPULSE()->name():
                if($this->isRedstoneMode){
                    //TODO redstone
                    return false;
                }
                return !$this->ranCommand;
            case CommandBlockType::REPEAT()->name():
                return !$this->isRedstoneMode;
        }
        return false;
    }

    public function getDefaultName(): string{
        return "";
    }

    /**
     * @throws Exception
     */
    public function getType(): CommandBlockType{
        return $this->getCommandBlock()->getType();
    }

    /**
     * @throws Exception
     */
    public function getCommandBlock(): CommandBlock{
        $block = $this->getBlock();

        if($block instanceof CommandBlock){
            return $block;
        }
        throw new Exception("Command Block cannot be found");
    }
}