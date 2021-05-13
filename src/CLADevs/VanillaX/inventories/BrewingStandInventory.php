<?php

namespace CLADevs\VanillaX\inventories;

use CLADevs\VanillaX\blocks\tiles\BrewingStandTile;
use CLADevs\VanillaX\blocks\types\BrewingStandBlock;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\NetworkInventoryAction;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class BrewingStandInventory extends FakeBlockInventory{

    const INGREDIENT_SLOT = 0;

    const FIRST_POTION_SLOT = 1;
    const SECOND_POTION_SLOT = 2;
    const THIRD_POTION_SLOT = 3;

    const FUEL_SLOT = 4;

    private BrewingStandTile $tile;

    public function __construct(BrewingStandTile $tile){
        parent::__construct($tile, 5, BlockIds::AIR, WindowTypes::BREWING_STAND);
        $this->tile = $tile;
    }

    public function isPotion(Item $item): bool{
        return in_array($item->getId(), [ItemIds::POTION, ItemIds::SPLASH_POTION, ItemIds::LINGERING_POTION]);
    }

    public function onOpen(Player $who): void{
        parent::onOpen($who);
        if($this->tile->isBrewing()){
            $this->sendBrewTimeData($who, $this->tile->getBrewTime());
        }
        $this->sendFuelAmount($who, $this->tile->getFuelAmount());
        $this->sendFuelTotal($who, $this->tile->getFuelTotal());
    }

    public function handlePacket(Player $player, DataPacket $packet): bool{
        if($packet instanceof InventoryTransactionPacket){
            foreach($packet->trData->getActions() as $action){
                if($action->sourceType === NetworkInventoryAction::SOURCE_CONTAINER && $action->windowId === WindowTypes::BREWING_STAND){
                    $ingredient = $this->getItem(self::INGREDIENT_SLOT);
                    $newItem = $action->newItem->getItemStack();

                    switch($action->inventorySlot){
                        case self::FIRST_POTION_SLOT:
                        case self::SECOND_POTION_SLOT:
                        case self::THIRD_POTION_SLOT:
                            $block = $player->getLevel()->getBlock($this->getHolder());

                            if($block instanceof BrewingStandBlock){
                                /** Nukkit Reference */
                                $damage = 0;

                                for($i = self::FIRST_POTION_SLOT; $i <= self::THIRD_POTION_SLOT; $i++){
                                    if($action->inventorySlot === $i){
                                        $potion = $newItem;
                                    }else{
                                        $potion = $this->getItem($i);
                                    }

                                    if(!$potion->isNull() && $this->isPotion($potion)){
                                        $damage |= 1 << ($i - 1);
                                    }
                                }
                                $block->setDamage($damage);
                            }
                            $player->getLevel()->setBlock($block, $block, true, true);
                            break;
                        case self::FUEL_SLOT:
                            if(!$this->tile->isFueled() && $newItem->getId() === ItemIds::BLAZE_POWDER){
                                $this->tile->resetFuel(true);
                            }
                            break;
                        case self::INGREDIENT_SLOT:
                            $ingredient = $newItem;
                            break;
                    }
                    $potionCount = 0;

                    for($i = self::FIRST_POTION_SLOT; $i <= self::THIRD_POTION_SLOT; $i++){
                        if($this->isPotion($this->getItem($i))){
                            $potionCount++;
                            if($action->inventorySlot === $i && !$this->isPotion($newItem)){
                                $potionCount--;
                            }
                        }elseif($action->inventorySlot === $i && $this->isPotion($newItem)){
                            $potionCount++;
                        }
                    }
                    $this->tile->checkBrewing($potionCount, $ingredient);
                }
            }
        }
        return true;
    }

    private function sendContainerSetDataPacket(?Player $player, int $start, int $end, int $amount, ?callable $callable = null): void{
        for($i = $start; $i <= $end; $i++){
            $pk = new ContainerSetDataPacket();
            $pk->windowId = WindowTypes::BREWING_STAND;
            $pk->property = $i;
            if($callable === null){
                $pk->value = $amount;
            }else{
                $callable($pk, $amount, $i);
            }

            if($player === null){
                foreach($this->getViewers() as $p){
                    $p->dataPacket($pk);
                }
            }else{
                $player->dataPacket($pk);
            }
        }
    }

    public function sendFuelData(?Player $player, int $amount, bool $total = true): void{
        $this->sendContainerSetDataPacket($player, 1, $total ? 2 : 1, $amount);
    }

    public function sendFuelAmount(?Player $player, int $amount): void{
        $this->sendContainerSetDataPacket($player, 1, 1, $amount);
    }

    public function sendFuelTotal(?Player $player, int $amount): void{
        $this->sendContainerSetDataPacket($player, 2, 2, $amount);
    }

    public function sendBrewTimeData(?Player $player, int $amount): void{
        $this->sendContainerSetDataPacket($player, 0, 0, $amount);
    }
}