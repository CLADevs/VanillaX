<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\blocks\tile\BrewingStandTile;
use CLADevs\VanillaX\blocks\block\BrewingStandBlock;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\BrewingStandSlot;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ContainerSetDataPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\ServerboundPacket;
use pocketmine\network\mcpe\protocol\types\inventory\NetworkInventoryAction;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;

class BrewingStandInventory extends FakeBlockInventory{

    const INGREDIENT_SLOT = 0;

    const FIRST_POTION_SLOT = 1;
    const SECOND_POTION_SLOT = 2;
    const THIRD_POTION_SLOT = 3;

    const FUEL_SLOT = 4;

    private BrewingStandTile $tile;

    public function __construct(BrewingStandTile $tile){
        parent::__construct($tile->getPosition(), 5, BlockLegacyIds::AIR, WindowTypes::BREWING_STAND);
        $this->tile = $tile;
    }

    public function getIngredient(): Item{
        return $this->getItem(self::INGREDIENT_SLOT);
    }

    public function setIngredient(Item $item): void{
        $this->setItem(self::INGREDIENT_SLOT, $item);
    }

    public function decreaseIngredient(): void{
        $item = $this->getIngredient();
        $item->setCount($item->getCount() - 1);
        $this->setIngredient($item);
    }

    public function getFuel(): Item{
        return $this->getItem(self::FUEL_SLOT);
    }

    public function setFuel(Item $item): void{
        $this->setItem(self::FUEL_SLOT, $item);
    }

    public function decreaseFuel(): void{
        $item = $this->getFuel();
        $item->setCount($item->getCount() - 1);
        $this->setFuel($item);
    }

    public function isPotion(Item $item, ?Item $ingredient = null): bool{
        $potion = in_array($item->getId(), [ItemIds::POTION, ItemIds::SPLASH_POTION, ItemIds::LINGERING_POTION]);

        if($ingredient !== null){
            $inventory = VanillaX::getInstance()->getInventoryManager();
            if(($output = $inventory->getBrewingOutput($item, $ingredient)) === null){
                $output = $inventory->getBrewingContainerOutput($item, $ingredient);
            }
            $potion = $output !== null;
        }
        return $potion;
    }

    public function onOpen(Player $who): void{
        parent::onOpen($who);
        if($this->tile->isBrewing()){
            $this->sendBrewTimeData($who, $this->tile->getBrewTime());
        }
        $this->sendFuelAmount($who, $this->tile->getFuelAmount());
        $this->sendFuelTotal($who, $this->tile->getFuelTotal());
    }

    public function handlePacket(Player $player, ServerboundPacket $packet): bool{
        if($packet instanceof InventoryTransactionPacket){
            foreach($packet->trData->getActions() as $action){
                $window = $player->getNetworkSession()->getInvManager()->getWindow($action->windowId);

                if($action->sourceType === NetworkInventoryAction::SOURCE_CONTAINER && $window instanceof BrewingStandInventory){
                    $ingredient = $this->getIngredient();
                    $newItem = TypeConverter::getInstance()->netItemStackToCore($action->newItem->getItemStack());

                    switch($action->inventorySlot){
                        case self::FIRST_POTION_SLOT:
                        case self::SECOND_POTION_SLOT:
                        case self::THIRD_POTION_SLOT:
                            $block = $player->getWorld()->getBlock($this->getHolder());

                            if($block instanceof BrewingStandBlock){
                                $slot = match($action->inventorySlot){
                                    self::FIRST_POTION_SLOT => BrewingStandSlot::NORTHWEST(),
                                    self::SECOND_POTION_SLOT => BrewingStandSlot::EAST(),
                                    self::THIRD_POTION_SLOT => BrewingStandSlot::SOUTHWEST(),
                                };
                                $block->setSlot($slot, $action->newItem->getItemStack()->getId() !== 0);
                                $player->getWorld()->setBlock($block->getPosition(), $block);
                            }
                            break;
                        case self::FUEL_SLOT:
                            if(!$this->tile->isFueled() && $newItem->getId() === ItemIds::BLAZE_POWDER){
                                $this->tile->resetFuel(true);
                            }
                            break;
                        case self::INGREDIENT_SLOT:
                            $ingredient = $newItem;

                            if(!$action->oldItem->getItemStack()->equals(TypeConverter::getInstance()->coreItemStackToNet($newItem)) && $this->tile->isBrewing()){
                                $this->tile->stopBrewing();
                            }
                            break;
                    }
                    $potionCount = 0;

                    for($i = self::FIRST_POTION_SLOT; $i <= self::THIRD_POTION_SLOT; $i++){
                        if($this->isPotion($this->getItem($i), $ingredient)){
                            $potionCount++;
                            if($action->inventorySlot === $i && !$this->isPotion($newItem, $ingredient)){
                                $potionCount--;
                            }
                        }elseif($action->inventorySlot === $i && $this->isPotion($newItem, $ingredient)){
                            $potionCount++;
                        }
                    }
                    $this->tile->checkBrewing($potionCount, $ingredient);
                }
            }
        }
        return true;
    }

    public function sendFuelAmount(?Player $player, int $amount): void{
        $this->sendData($player, ContainerSetDataPacket::PROPERTY_BREWING_STAND_FUEL_AMOUNT, $amount);
    }

    public function sendFuelTotal(?Player $player, int $total): void{
        $this->sendData($player, ContainerSetDataPacket::PROPERTY_BREWING_STAND_FUEL_TOTAL, $total);
    }

    public function sendBrewTimeData(?Player $player, int $time): void{
        $this->sendData($player, ContainerSetDataPacket::PROPERTY_BREWING_STAND_BREW_TIME, $time);
    }

    public function sendData(?Player $player, int $property, int $value): void{
        $pk = ContainerSetDataPacket::create(-1, $property, $value);

        foreach(($player === null ? $this->getViewers() : [$player]) as $p){
            $pk->windowId = $p->getNetworkSession()->getInvManager()->getCurrentWindowId();
            $p->getNetworkSession()->sendDataPacket($pk);
        }
    }
}
