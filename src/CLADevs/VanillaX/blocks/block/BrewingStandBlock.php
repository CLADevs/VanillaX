<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\BrewingStandTile;
use CLADevs\VanillaX\inventories\types\BrewingStandInventory;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\BrewingStand;
use pocketmine\block\utils\AnyFacingTrait;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\player\Player;

class BrewingStandBlock extends BrewingStand{
    use AnyFacingTrait;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::BREWING_STAND_BLOCK, 0, ItemIds::BREWING_STAND, BrewingStandTile::class), "Brewing Stand", new BlockBreakInfo(0.5, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel()));
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        if($player !== null){
            $tile = $this->pos->getWorld()->getTile($this->pos);

            if($tile instanceof BrewingStandTile){
                $player->setCurrentWindow($tile->getInventory());
            }
        }
        return true;
    }

    public function onScheduledUpdate(): void{
        $tile = $this->pos->getWorld()->getTile($this->pos);

        if($tile->isClosed() || !$tile instanceof BrewingStandTile){
            return;
        }
        $inventory = $tile->getInventory();

        if($tile->canRemoveFuel() && !$inventory->getFuel()->isNull()){
            $inventory->decreaseFuel();
            $tile->setRemoveFuel(false);
        }
        if($tile->isBrewing()){
            $tile->decreaseBrewTime();

            if($tile->getBrewTime() <= 0){
                $ingredient = $inventory->getIngredient();

                for($i = BrewingStandInventory::FIRST_POTION_SLOT; $i <= BrewingStandInventory::THIRD_POTION_SLOT; $i++){
                    $potion = $inventory->getItem($i);

                    if($inventory->isPotion($potion, $ingredient)){
                        $inventoryManager = VanillaX::getInstance()->getInventoryManager();

                        if(($output = $inventoryManager->getBrewingOutput($potion, $ingredient)) === null){
                            $output = $inventoryManager->getBrewingContainerOutput($potion, $ingredient);
                        }
                        if($output !== null){
                            $inventory->setItem($i, $output);
                        }
                    }
                }
                $inventory->decreaseIngredient();
                $tile->setFuelAmount($tile->getFuelAmount() - 1, true);
                $tile->getPos()->getWorld()->broadcastPacketToViewers($tile->getPos(), LevelSoundEventPacket::create(LevelSoundEventPacket::SOUND_POTION_BREWED, $tile->getPos()));

                if($tile->getFuelAmount() <= 0 && !$inventory->getFuel()->isNull()){
                    $inventory->decreaseFuel();
                    $tile->resetFuel();
                }
            }
        }
        $this->pos->getWorld()->scheduleDelayedBlockUpdate($this->pos, 1);
    }
}