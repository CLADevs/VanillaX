<?php

namespace CLADevs\VanillaX\blocks\tiles;

use CLADevs\VanillaX\blocks\TileIdentifiers;
use CLADevs\VanillaX\blocks\types\CommandBlock;
use CLADevs\VanillaX\commands\CommandBlockSender;
use pocketmine\block\BlockFactory;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\Server;
use pocketmine\tile\Nameable;
use pocketmine\tile\NameableTrait;
use pocketmine\tile\Spawnable;

class CommandBlockTile extends Spawnable implements Nameable{
    use NameableTrait;

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

    /** CUSTOM TAGS */
    const TAG_COMMAND_BLOCK_MODE = "commandBlockMode";

    public int $commandBlockMode = self::TYPE_IMPULSE;

    /** @var NamedTag[] */
    private array $lastOutputParam = [];

    public string $command = "";
    public bool $executeOnFirstTick = false;
    public bool $LPCommandMode = false;
    public bool $LPConditionalMode = false;
    public bool $LPRedstoneMode = false;
    public int $lastExecution = 0;
    public string $lastOutput = "";
    private int $successCount = 0;
    public int $tickDelay = 0;
    public bool $shouldTrackOutput = true;
    public bool $auto = false;
    public bool $conditionMet = false;
    public bool $conditionalMode = false;

    private bool $isScheduled = false;
    private int $countDelayTick = 0;

    public function onUpdate(): bool{
        if($this->closed || !$this->isScheduled){
            return false;
        }
        if($this->tickDelay > 0 && $this->countDelayTick > 0){
            $this->countDelayTick--;
            return true;
        }
        $this->runCommand();
        if($this->commandBlockMode === self::TYPE_REPEAT){
            $this->countDelayTick = $this->tickDelay;
        }else{
            $this->isScheduled = false;
            return false;
        }
        return true;
    }

    public function runCommand(): void{
        if(strlen($this->command) > 0){
            $sender = new CommandBlockSender();
            if(Server::getInstance()->dispatchCommand($sender, $this->command)){
                $this->successCount++;
            }
            $this->lastOutput = $sender->getMessage();
        }
    }

    public function handleCommandBlockUpdateReceive(CommandBlockUpdatePacket $pk): void{
        if($pk->commandBlockMode !== $this->commandBlockMode){
            $this->commandBlockMode = $pk->commandBlockMode;
            $tileBlock = $this->getLevel()->getBlock($this);
            $block = BlockFactory::get(CommandBlock::asCommandBlockFromMode($this->commandBlockMode), $tileBlock->getDamage());

            if($tileBlock instanceof CommandBlock){
                $block->setDamage($tileBlock->getDamage());
            }
            $this->getLevel()->setBlock($this, $block, true, true);
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
            $tileBlock = $this->getLevel()->getBlock($this);
            $block = BlockFactory::get(CommandBlock::asCommandBlockFromMode($this->commandBlockMode));
            $block->setDamage($tileBlock->getDamage() + ($pk->isConditional ? 8 : -8));
            $this->getLevel()->setBlock($this, $block, true, true);
        }
        if($this->tickDelay == 0 && strlen($this->command) >= 1){
            $this->runCommand();
        }elseif(($this->tickDelay >= 1 && strlen($this->command) >= 1 && !$this->isScheduled) || $this->commandBlockMode == self::TYPE_REPEAT){
            $this->scheduleUpdate();
            $this->isScheduled = true;
        }
        $this->onChanged();
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
    }

    protected function readSaveData(CompoundTag $nbt): void{
        $this->loadName($nbt);
        if($nbt->hasTag($tag = self::TAG_COMMAND_BLOCK_MODE)){
            $this->commandBlockMode = $nbt->getInt($tag);
        }
        if($nbt->hasTag($tag = self::TAG_COMMAND)){
            $this->command = $nbt->getString($tag);
        }
        if($nbt->hasTag($tag = self::TAG_EXECUTE_ON_FIRE_TICK)){
            $this->executeOnFirstTick = boolval($nbt->getInt($tag));
        }
        if($nbt->hasTag($tag = self::TAG_LAST_EXECUTION)){
            $this->lastExecution = $nbt->getInt($tag);
        }
        if($nbt->hasTag($tag = self::TAG_SUCCESS_COUNT)){
            $this->successCount = $nbt->getInt($tag);
        }
        if($nbt->hasTag($tag = self::TAG_TICK_DELAY)){
            $this->tickDelay = $nbt->getInt($tag);
        }
        if($nbt->hasTag($tag = self::TAG_TRACK_OUTPUT)){
            $this->shouldTrackOutput = boolval($nbt->getInt($tag));
        }
        if($nbt->hasTag($tag = self::TAG_AUTO)){
            $this->auto = boolval($nbt->getInt($tag));
        }
        if($nbt->hasTag($tag = self::TAG_CONDITION_MET)){
            $this->conditionMet = boolval($nbt->getInt($tag));
        }
        if($nbt->hasTag($tag = self::TAG_CONDITIONAL_MODE)){
            $this->conditionalMode = boolval($nbt->getInt($tag));
        }
        if($this->commandBlockMode === self::TYPE_REPEAT){
            $this->scheduleUpdate();
            $this->isScheduled = true;
        }
        $this->onChanged();
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
            $nbt->setTag(new ListTag(self::TAG_LAST_OUTPUT_PARAMS, $this->lastOutputParam));
        }
        $nbt->setInt(self::TAG_SUCCESS_COUNT, $this->successCount);
        $nbt->setInt(self::TAG_TICK_DELAY, $this->tickDelay);
        $nbt->setByte(self::TAG_TRACK_OUTPUT, intval($this->shouldTrackOutput));
        $nbt->setByte(self::TAG_AUTO, intval($this->auto));
        $nbt->setByte(self::TAG_CONDITION_MET, intval($this->conditionMet));
        $nbt->setByte(self::TAG_CONDITIONAL_MODE, intval($this->conditionalMode));
    }

    public static function generateTile(Position $position, int $commandBlockMode): CommandBlockTile{
        $nbt = new CompoundTag("", [
            new StringTag("id", TileIdentifiers::COMMAND_BLOCK),
            new IntTag("commandBlockMode", $commandBlockMode),
            new IntTag("x", $position->x),
            new IntTag("y", $position->y),
            new IntTag("z", $position->z),
        ]);
        return new self($position->getLevel(), $nbt);
    }

    public function getDefaultName(): string{
        return "";
    }
}