<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\HopperTile;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;
use pocketmine\block\utils\FacesOppositePlacingPlayerTrait;
use pocketmine\block\utils\NormalHorizontalFacingInMetadataTrait;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class HopperBlock extends Transparent{
    use FacesOppositePlacingPlayerTrait;
    use NormalHorizontalFacingInMetadataTrait;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::HOPPER_BLOCK, 0, ItemIds::HOPPER, HopperTile::class), "Hopper", new BlockBreakInfo(3, BlockToolType::PICKAXE, 0, 4.8));
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($player !== null){
            $tile = $this->getPos()->getWorld()->getTile($this->getPos());

            if($tile instanceof HopperTile){
                $player->setCurrentWindow($tile->getInventory());
            }
        }
        return true;
    }

    public function onScheduledUpdate(): void{
        $tile = $this->pos->getWorld()->getTile($this->pos);

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
        $bb = new AxisAlignedBB($this->pos->x, $this->pos->y, $this->pos->z, $this->pos->x + 1, $this->pos->y +2, $this->pos->z + 1);

        foreach($this->pos->getWorld()->getNearbyEntities($bb) as $entity){
            if(!$entity->isClosed() && !$entity->isFlaggedForDespawn() && $entity instanceof ItemEntity){
                $item = $entity->getItem();

                if(!$item->isNull()){
                    $inventory->addItem($item);
                    $entity->flagForDespawn();
                }
            }
        }
        $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, 1);
    }
}