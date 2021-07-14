<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\CommandBlockTile;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\utils\FacesOppositePlacingPlayerTrait;
use pocketmine\block\utils\NormalHorizontalFacingInMetadataTrait;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\types\inventory\ContainerIds;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;

class CommandBlock extends Block implements NonAutomaticCallItemTrait, NonCreativeItemTrait{
    use FacesOppositePlacingPlayerTrait;
    use NormalHorizontalFacingInMetadataTrait;

    public function __construct(int $id){
        parent::__construct(new BlockIdentifier($id, 0, $id, CommandBlockTile::class), self::asCommandBlockName($id), new BlockBreakInfo(-1, BlockToolType::NONE, 0, 3600000));
    }

    public function getMode(): int{
        if($this->getId() == BlockLegacyIds::REPEATING_COMMAND_BLOCK){
            return CommandBlockTile::TYPE_REPEAT;
        }elseif($this->getId() == BlockLegacyIds::CHAIN_COMMAND_BLOCK){
            return CommandBlockTile::TYPE_CHAIN;
        }
        return CommandBlockTile::TYPE_IMPULSE;
    }

    public static function asCommandBlockName(int $id): string{
        if($id === BlockLegacyIds::REPEATING_COMMAND_BLOCK){
            return "Repeating Command Block";
        }elseif($id === BlockLegacyIds::CHAIN_COMMAND_BLOCK){
            return "Chain Command Block";
        }
        return "Command Block";
    }

    public static function asCommandBlockFromMode(int $mode): int{
        if($mode == CommandBlockTile::TYPE_REPEAT){
            return BlockLegacyIds::REPEATING_COMMAND_BLOCK;
        }elseif($mode == CommandBlockTile::TYPE_CHAIN){
            return BlockLegacyIds::CHAIN_COMMAND_BLOCK;
        }
        return BlockLegacyIds::COMMAND_BLOCK;
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($player !== null && $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $tile = $this->getPos()->getWorld()->getTile($this->getPos());

            $pk = new ContainerOpenPacket();
            $pk->type = WindowTypes::COMMAND_BLOCK;
            $pk->windowId = ContainerIds::NONE;
            $pk->x = $tile->getPos()->x;
            $pk->y = $tile->getPos()->y;
            $pk->z = $tile->getPos()->z;
            $player->getNetworkSession()->sendDataPacket($pk);
        }
        return true;
    }

    public function onScheduledUpdate(): void{
        $tile = $this->pos->getWorld()->getTile($this->pos);

        if($tile->isClosed() || !$tile instanceof CommandBlockTile){
            return;
        }
        if($tile->getTickDelay() > 0 && $tile->getCountDelayTick() > 0){
            $tile->decreaseCountDelayTick();
        }else{;
            $tile->runCommand();
            if($tile->getCommandBlockMode() === CommandBlockTile::TYPE_REPEAT){
                $tile->setCountDelayTick($tile->getTickDelay());
                $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, 1);
            }
        }
    }
}