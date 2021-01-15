<?php

namespace CLADevs\VanillaX\blocks\tiles;

use CLADevs\VanillaX\blocks\TileIdentifiers;
use CLADevs\VanillaX\blocks\types\CommandBlock;
use pocketmine\block\BlockFactory;
use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\CommandBlockUpdatePacket;
use pocketmine\tile\Spawnable;

class CommandBlockTile extends Spawnable{

    /** TYPES OF COMMAND BLOCKS */
    const IMPULSE = 0;
    const REPEAT = 1;
    const CHAIN = 2;

    public int $commandBlockMode = self::IMPULSE;
    public int $tickDelay = 0;
    public bool $isRedstoneMode = true;
    public bool $isConditional = false;
    public bool $shouldTrackOutput = true;
    public bool $executeOnFirstTick = true;
    public string $commandBlockName = "";
    public string $command = "";
    public string $lastOutput = "";

    public function handleCommandBlockUpdateReceive(CommandBlockUpdatePacket $pk): void{
        if($pk->commandBlockMode !== $this->commandBlockMode){
            $this->commandBlockMode = $pk->commandBlockMode;
            $this->getLevel()->setBlock($this, BlockFactory::get(CommandBlock::asCommandBlockFromMode($this->commandBlockMode)), true, true);
        }
        if($pk->tickDelay !== $this->tickDelay){
            $this->tickDelay = $pk->tickDelay;
        }
        if($pk->isRedstoneMode !== $this->isRedstoneMode){
            $this->isRedstoneMode = $pk->isRedstoneMode;
        }
        if($pk->isConditional !== $this->isConditional){
            $this->isConditional = $pk->isConditional;
        }
        if($pk->shouldTrackOutput !== $this->shouldTrackOutput){
            $this->shouldTrackOutput = $pk->shouldTrackOutput;
        }
        if($pk->executeOnFirstTick !== $this->executeOnFirstTick){
            $this->executeOnFirstTick = $pk->executeOnFirstTick;
        }
        if($pk->name !== $this->commandBlockName){
            $this->commandBlockName = $pk->name;
        }
        if($pk->command !== $this->command){
            $this->command = $pk->command;
        }
        if($pk->lastOutput !== $this->lastOutput){
            $this->lastOutput = $pk->lastOutput;
        }
        $this->onChanged();
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $nbt->setInt("commandBlockMode", $this->commandBlockMode);
        $nbt->setInt("tickDelay", $this->tickDelay);
        $nbt->setInt("isRedstoneMode", $this->isRedstoneMode);
        $nbt->setInt("isConditional", $this->isConditional);
        $nbt->setInt("shouldTrackOutput", $this->shouldTrackOutput);
        $nbt->setInt("executeOnFirstTick", $this->executeOnFirstTick);
        $nbt->setString("name", $this->commandBlockName);
        $nbt->setString("command", $this->command);
        $nbt->setString("lastOutput", $this->lastOutput);
    }

    protected function readSaveData(CompoundTag $nbt): void{
        if($nbt->hasTag($tag = "commandBlockMode")){
            $this->commandBlockMode = $nbt->getInt($tag);
        }
        if($nbt->hasTag($tag = "tickDelay")){
            $this->tickDelay = $nbt->getInt($tag);
        }
        if($nbt->hasTag($tag = "isRedstoneMode")){
            $this->isRedstoneMode = boolval($nbt->getInt($tag));
        }
        if($nbt->hasTag($tag = "isConditional")){
            $this->isConditional = boolval($nbt->getInt($tag));
        }
        if($nbt->hasTag($tag = "shouldTrackOutput")){
            $this->shouldTrackOutput = boolval($nbt->getInt($tag));
        }
        if($nbt->hasTag($tag = "executeOnFirstTick")){
            $this->executeOnFirstTick = boolval($nbt->getInt($tag));
        }
        if($nbt->hasTag($tag = "name")){
            $this->commandBlockName = $nbt->getString($tag);
        }
        if($nbt->hasTag($tag = "command")){
            $this->command = $nbt->getString($tag);
        }
        if($nbt->hasTag($tag = "lastOutput")){
            $this->lastOutput = $nbt->getString($tag);
        }
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
        $nbt->setString("CustomName", $this->commandBlockName);
        $nbt->setString("Command", $this->command);
        //TODO add more tags, idk what are other tags LMAO
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
}