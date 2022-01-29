<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\CommandBlockTile;
use CLADevs\VanillaX\blocks\utils\AnyFacingTrait;
use CLADevs\VanillaX\blocks\utils\CommandBlockType;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use Exception;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\inventory\ContainerIds;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class CommandBlock extends Block implements NonAutomaticCallItemTrait, NonCreativeItemTrait{
    use AnyFacingTrait;

    public function __construct(CommandBlockType $type, int $meta){
        parent::__construct(new BlockIdentifier($type->getBlockId(), $meta, $type->getBlockId(), CommandBlockTile::class), $type->getDisplayName(), new BlockBreakInfo(-1, BlockToolType::NONE, 0, 3600000));
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($player !== null && $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            if(in_array($face, [Facing::UP, Facing::DOWN])){
                $this->facing = $face;
            }else{
                $this->facing = Facing::opposite($player->getHorizontalFacing());
            }
            return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        }
        return false;
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($player !== null && $player->hasPermission(DefaultPermissions::ROOT_OPERATOR)){
            $player->getNetworkSession()->sendDataPacket(
                ContainerOpenPacket::blockInv(
                    ContainerIds::NONE,
                    WindowTypes::COMMAND_BLOCK,
                    BlockPosition::fromVector3($this->getPosition())
                )
            );
        }
        return true;
    }

    /**
     * @throws Exception
     */
    public function onScheduledUpdate(): void{
        $tile = $this->position->getWorld()->getTile($this->position);

        if(!$tile instanceof CommandBlockTile || $tile->isClosed()){
            return;
        }
        if($tile->getTickDelay() > 0 && $tile->getCountDelayTick() > 0){
            $tile->decreaseCountDelayTick();
        }else{
            $tile->runCommand();
            if($tile->getType()->isRepeating()){
                $tile->setCountDelayTick($tile->getTickDelay());
                $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1);
            }
        }
    }

    public function getType(): CommandBlockType{
        return CommandBlockType::fromBlock($this);
    }
}