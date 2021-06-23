<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\CommandBlockTile;
use CLADevs\VanillaX\blocks\utils\TileUtils;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\IntTag;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class CommandBlock extends Block implements NonAutomaticCallItemTrait, NonCreativeItemTrait{

    public function __construct(int $id){
        parent::__construct($id, 0, self::asCommandBlockName($id), $id);
    }

    public function getMode(): int{
        if($this->getId() == BlockIds::REPEATING_COMMAND_BLOCK){
            return CommandBlockTile::TYPE_REPEAT;
        }elseif($this->getId() == BlockIds::CHAIN_COMMAND_BLOCK){
            return CommandBlockTile::TYPE_CHAIN;
        }
        return CommandBlockTile::TYPE_IMPULSE;
    }

    public static function asCommandBlockName(int $id): string{
        if($id === BlockIds::REPEATING_COMMAND_BLOCK){
            return "Repeating Command Block";
        }elseif($id === BlockIds::CHAIN_COMMAND_BLOCK){
            return "Chain Command Block";
        }
        return "Command Block";
    }

    public static function asCommandBlockFromMode(int $mode): int{
        if($mode == CommandBlockTile::TYPE_REPEAT){
            return BlockIds::REPEATING_COMMAND_BLOCK;
        }elseif($mode == CommandBlockTile::TYPE_CHAIN){
            return BlockIds::CHAIN_COMMAND_BLOCK;
        }
        return BlockIds::COMMAND_BLOCK;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        $faces = [
            Vector3::SIDE_DOWN => 0,
            Vector3::SIDE_UP => 1,
            Vector3::SIDE_NORTH => 2,
            Vector3::SIDE_SOUTH => 3,
            Vector3::SIDE_WEST => 4,
            Vector3::SIDE_EAST => 5
        ];
        $this->meta = $faces[$face] ?? $face;
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        TileUtils::generateTile($blockReplace, TileVanilla::COMMAND_BLOCK, CommandBlockTile::class, [new IntTag(CommandBlockTile::TAG_COMMAND_BLOCK_MODE, $this->getMode())]);
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null && $player->isOp()){
            $tile = $this->getLevel()->getTile($this);

            if(!$tile instanceof CommandBlockTile){
                $tile = TileUtils::generateTile($this, TileVanilla::COMMAND_BLOCK, CommandBlockTile::class, [new IntTag(CommandBlockTile::TAG_COMMAND_BLOCK_MODE, $this->getMode())]);
            }
            $pk = new ContainerOpenPacket();
            $pk->type = WindowTypes::COMMAND_BLOCK;
            $pk->windowId = ContainerIds::NONE;
            $pk->x = $tile->x;
            $pk->y = $tile->y;
            $pk->z = $tile->z;
            $player->dataPacket($pk);
        }
        return true;
    }
}