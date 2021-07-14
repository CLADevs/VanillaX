<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\block\CommandBlock;
use CLADevs\VanillaX\blocks\BlockManager;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\commands\sender\CommandBlockSender;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\NameableTrait;
use pocketmine\block\tile\Spawnable;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\Server;

class CommandBlockTile extends Spawnable implements Nameable{
    use NameableTrait;

    const TILE_ID = TileVanilla::COMMAND_BLOCK;
    const TILE_BLOCK = [BlockLegacyIds::COMMAND_BLOCK, BlockLegacyIds::CHAIN_COMMAND_BLOCK, BlockLegacyIds::REPEATING_COMMAND_BLOCK];

    /** TYPES OF COMMAND BLOCKS */
    const TYPE_IMPULSE = 0;
    const TYPE_REPEAT = 1;
    const TYPE_CHAIN = 2;

    /** VANILLA TAGS */
    const TAG_COMMAND = "Command";
    const TAG_EXECUTE_ON_FIRE_TICK = "ExecuteOnFirstTick";
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

    /** CUSTOM TAGS */
    const TAG_COMMAND_BLOCK_MODE = "commandBlockMode";

    /** @var Tag[] */
    private array $lastOutputParam = [];
    
    private int $commandBlockMode = self::TYPE_IMPULSE;
    private int $lastExecution = 0;
    private int $successCount = 0;
    private int $tickDelay = 0;
    private int $countDelayTick = 0;

    private string $command = "";
    private string $lastOutput = "";
    
    private bool $executeOnFirstTick = false;
    private bool $LPCommandMode = false;
    private bool $LPConditionalMode = false;
    private bool $LPRedstoneMode = false;
    private bool $shouldTrackOutput = true;
    private bool $auto = false;
    private bool $conditionMet = false;
    private bool $conditionalMode = false;
    private bool $ranCommand = false;

    public function getDefaultName(): string{
        return "Command Block";
    }

    public function runCommand(): void{
        if(strlen($this->command) > 0){
            if($this->ranCommand){
               return;
            }
            $sender = new CommandBlockSender(Server::getInstance(), Server::getInstance()->getLanguage());
            if(Server::getInstance()->dispatchCommand($sender, $this->command)){
                $this->successCount++;
            }
            $this->lastOutput = $sender->getMessage();
            if($this->commandBlockMode !== self::TYPE_REPEAT){
                $this->ranCommand = true;
            }
        }
    }

    public function handleCommandBlockUpdateReceive(CommandBlockUpdatePacket $pk): void{
        if($pk->commandBlockMode !== $this->commandBlockMode){
            $this->ranCommand = false;
            $this->commandBlockMode = $pk->commandBlockMode;
            $tileBlock = $this->getPos()->getWorld()->getBlock($this->getPos());
            /** @var CommandBlock $block */
            $block = BlockFactory::getInstance()->get(CommandBlock::asCommandBlockFromMode($this->commandBlockMode), $tileBlock->getMeta());

            if($tileBlock instanceof CommandBlock){
                $block->setFacing($tileBlock->getFacing());
            }
            $this->getPos()->getWorld()->setBlock($this->getPos(), $block);
        }
        if($pk->name !== $this->getName()){
            $this->setName($pk->name);
        }
        if($pk->command !== $this->command){
            $this->command = $pk->command;
        }
        if($pk->executeOnFirstTick !== $this->executeOnFirstTick){
            $this->executeOnFirstTick = $pk->executeOnFirstTick;
        }
        if($pk->lastOutput !== $this->lastOutput){
            $this->lastOutput = $pk->lastOutput;
        }
        if($pk->tickDelay !== $this->tickDelay){
            $this->tickDelay = $pk->tickDelay;
        }
        if($pk->shouldTrackOutput !== $this->shouldTrackOutput){
            $this->shouldTrackOutput = $pk->shouldTrackOutput;
        }
        if($pk->isRedstoneMode !== $this->auto){
            $this->auto = $pk->isRedstoneMode;
        }
        if($pk->isConditional !== $this->conditionalMode){
            $this->conditionalMode = $pk->isConditional;
            /** @var CommandBlock $tileBlock */
            $tileBlock = $this->getPos()->getWorld()->getBlock($this->getPos());
            $block = BlockFactory::getInstance()->get(CommandBlock::asCommandBlockFromMode($this->commandBlockMode), 0);
            $block->setFacing($tileBlock->getFacing() + ($pk->isConditional ? 8 : -8));
            $this->getPos()->getWorld()->setBlock($this->getPos(), $block);
        }
        if($this->tickDelay == 0 && strlen($this->command) >= 1){
            $this->runCommand();
        }
        $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, 1);
        $this->setDirty();
        BlockManager::onChange($this);
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->saveName($nbt);
        $nbt->setInt(self::TAG_COMMAND_BLOCK_MODE, $this->commandBlockMode);
        $nbt->setString(self::TAG_COMMAND, $this->command);
        $nbt->setInt(self::TAG_EXECUTE_ON_FIRE_TICK, $this->executeOnFirstTick);
        $nbt->setInt(self::TAG_LAST_EXECUTION, $this->lastExecution);
        $nbt->setInt(self::TAG_SUCCESS_COUNT, $this->successCount);
        $nbt->setInt(self::TAG_TICK_DELAY, $this->tickDelay);
        $nbt->setInt(self::TAG_TRACK_OUTPUT, $this->shouldTrackOutput);
        $nbt->setInt(self::TAG_AUTO, $this->auto);
        $nbt->setInt(self::TAG_CONDITION_MET, $this->conditionMet);
        $nbt->setInt(self::TAG_CONDITIONAL_MODE, $this->conditionalMode);
        $nbt->setInt(self::TAG_RAN_COMMAND, $this->ranCommand);
    }

    public function readSaveData(CompoundTag $nbt): void{
        $this->loadName($nbt);
        if(($tag = $nbt->getTag(self::TAG_COMMAND_BLOCK_MODE)) !== null){
            $this->commandBlockMode = $tag->getValue();
        }
        if(($tag = $nbt->getTag(self::TAG_COMMAND)) !== null){
            $this->command = $tag->getValue();
        }
        if(($tag = $nbt->getTag(self::TAG_EXECUTE_ON_FIRE_TICK)) !== null){
            $this->executeOnFirstTick = boolval($tag->getValue());
        }
        if(($tag = $nbt->getTag(self::TAG_LAST_EXECUTION)) !== null){
            $this->lastExecution = $tag->getValue();
        }
        if(($tag = $nbt->getTag(self::TAG_SUCCESS_COUNT)) !== null){
            $this->successCount = $tag->getValue();
        }
        if(($tag = $nbt->getTag(self::TAG_TICK_DELAY)) !== null){
            $this->tickDelay = $tag->getValue();
        }
        if(($tag = $nbt->getTag(self::TAG_TRACK_OUTPUT)) !== null){
            $this->shouldTrackOutput = boolval($tag->getValue());
        }
        if(($tag = $nbt->getTag(self::TAG_AUTO)) !== null){
            $this->auto = boolval($tag->getValue());
        }
        if(($tag = $nbt->getTag(self::TAG_CONDITION_MET)) !== null){
            $this->conditionMet = boolval($tag->getValue());
        }
        if(($tag = $nbt->getTag(self::TAG_CONDITIONAL_MODE)) !== null){
            $this->conditionalMode = boolval($tag->getValue());
        }
        if(($tag = $nbt->getTag(self::TAG_RAN_COMMAND)) !== null){
            $this->ranCommand = boolval($tag->getValue());
        }
        $this->setDirty();
        BlockManager::onChange($this);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        $nbt->setString(Nameable::TAG_CUSTOM_NAME, $this->getName());
        $nbt->setString(self::TAG_COMMAND, $this->command);
        $nbt->setByte(self::TAG_EXECUTE_ON_FIRE_TICK, intval($this->executeOnFirstTick));
        $nbt->setByte(self::TAG_LOOP_COMMAND_MODE, intval($this->LPCommandMode));
        $nbt->setByte(self::TAG_LOOP_CONDITIONAL_MODE, intval($this->LPConditionalMode));
        $nbt->setByte(self::TAG_LOOP_REDSTONE_MODE, intval($this->LPRedstoneMode));
        $nbt->setInt(self::TAG_LAST_EXECUTION, $this->lastExecution);
        $nbt->setString(self::TAG_LAST_OUTPUT, $this->lastOutput);
        if(count($this->lastOutputParam) >= 1){
            $nbt->setTag(self::TAG_LAST_OUTPUT_PARAMS, new ListTag($this->lastOutputParam));
        }
        $nbt->setInt(self::TAG_SUCCESS_COUNT, $this->successCount);
        $nbt->setInt(self::TAG_TICK_DELAY, $this->tickDelay);
        $nbt->setByte(self::TAG_TRACK_OUTPUT, intval($this->shouldTrackOutput));
        $nbt->setByte(self::TAG_AUTO, intval($this->auto));
        $nbt->setByte(self::TAG_CONDITION_MET, intval($this->conditionMet));
        $nbt->setByte(self::TAG_CONDITIONAL_MODE, intval($this->conditionalMode));
    }

    public function getTickDelay(): int{
        return $this->tickDelay;
    }

    public function getCountDelayTick(): int{
        return $this->countDelayTick;
    }

    public function setCountDelayTick(int $countDelayTick): void{
        $this->countDelayTick = $countDelayTick;
    }

    public function decreaseCountDelayTick(): void{
        $this->countDelayTick--;
    }

    public function getCommandBlockMode(): int{
        return $this->commandBlockMode;
    }

    public function hasRanCommand(): bool{
        return $this->ranCommand;
    }

    public function setRanCommand(bool $ranCommand): void{
        $this->ranCommand = $ranCommand;
    }
}
