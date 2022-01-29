<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\HopperTile;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;
use pocketmine\block\utils\AnyFacingTrait;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class HopperBlock extends Transparent{
    use AnyFacingTrait;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::HOPPER_BLOCK, 0, ItemIds::HOPPER, HopperTile::class), "Hopper", new BlockBreakInfo(3, BlockToolType::PICKAXE, 0, 4.8));
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        $this->facing = match($face){
            Facing::UP => Facing::DOWN,
            Facing::NORTH => Facing::SOUTH,
            Facing::SOUTH => Facing::NORTH,
            Facing::WEST => Facing::EAST,
            Facing::EAST => Facing::WEST,
        };
        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    protected function writeStateToMeta(): int{
        return $this->facing;
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($player !== null){
            $tile = $this->getPosition()->getWorld()->getTile($this->getPosition());

            if($tile instanceof HopperTile){
                $player->setCurrentWindow($tile->getInventory());
            }
        }
        return true;
    }

    public function onScheduledUpdate(): void{
        $tile = $this->position->getWorld()->getTile($this->position);

        if($tile->isClosed() || !$tile instanceof HopperTile){
            return;
        }
        $inventory = $tile->getInventory();
        //Transfer items
        if(count($inventory->getContents()) >= 1){
            $tile->decreaseTransferCooldown();
            if($tile->getTransferCooldown() < 1){
                $tile->setTransferCooldown(8);
                $tile->transferItems();
            }
        }

        //Thanks to nukkit for bounding box code
        //Collect dropped items
        $bb = new AxisAlignedBB($this->position->x, $this->position->y, $this->position->z, $this->position->x + 1, $this->position->y + 2, $this->position->z + 1);

        foreach($this->position->getWorld()->getNearbyEntities($bb) as $entity){
            if(!$entity->isClosed() && !$entity->isFlaggedForDespawn() && $entity instanceof ItemEntity){
                $item = $entity->getItem();

                if(!$item->isNull()){
                    $inventory->addItem($item);
                    $entity->flagForDespawn();
                }
            }
        }
        $this->position->getWorld()->scheduleDelayedBlockUpdate($this->position, 1);
    }
}