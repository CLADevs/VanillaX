<?php

namespace CLADevs\VanillaX\blocks\tiles;

use CLADevs\VanillaX\inventories\types\BrewingStandInventory;
use CLADevs\VanillaX\VanillaX;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\tile\Container;
use pocketmine\tile\ContainerTrait;
use pocketmine\tile\Nameable;
use pocketmine\tile\NameableTrait;
use pocketmine\tile\Spawnable;

class BrewingStandTile extends Spawnable implements Container, Nameable{
    use ContainerTrait, NameableTrait;

    const TAG_BREW_TIME = "BrewTime";
    const TAG_FUEL_AMOUNT = "FuelAmount";
    const TAG_FUEL_TOTAL = "FuelTotal";

    const MAX_BREW_TIME = 400; //20 seconds

    private BrewingStandInventory $inventory;
    private bool $removeFuel = false;

    private int $brewTime = 0;
    private int $fuelAmount = 0;
    private int $fuelTotal = 0;

    public function getInventory(): BrewingStandInventory{
        return $this->inventory;
    }

    public function getRealInventory(): BrewingStandInventory{
        return $this->inventory;
    }

    public function getDefaultName(): string{
        return "Brewing Stand";
    }

    public function isBrewing(): bool{
        return $this->brewTime >= 1;
    }

    public function getBrewTime(): int{
        return $this->brewTime;
    }

    public function setBrewTime(int $brewTime): void{
        $this->brewTime = $brewTime;
    }

    public function getFuelAmount(): int{
        return $this->fuelAmount;
    }

    public function setFuelAmount(int $fuelAmount, bool $send = false): void{
        $this->fuelAmount = $fuelAmount;
        if($send){
            $this->inventory->sendFuelAmount(null, $fuelAmount);
        }
    }

    public function getFuelTotal(): int{
        return $this->fuelTotal;
    }

    public function setFuelTotal(int $fuelTotal, bool $send = false): void{
        $this->fuelTotal = $fuelTotal;
        if($send){
            $this->inventory->sendFuelTotal(null, $fuelTotal);
        }
    }

    public function isFueled(): bool{
        return $this->fuelTotal >= 1 || $this->fuelAmount >= 1;
    }

    /**
     * @param bool $removeFuel, idk if there is a better method
     * If a player puts a fuel in fuel slot u cant decrease the count
     * so queueing it and removing it from task itself
     */
    public function resetFuel(bool $removeFuel = false): void{
        $this->inventory->sendFuelData(null, 20);
        $this->setFuelAmount(20);
        $this->setFuelTotal(20);
        if($removeFuel){
            $this->removeFuel = true;
        }
    }

    public function checkBrewing(int $potionCount, Item $ingredient): void{
        if(!$this->isBrewing()){
            if($potionCount >= 1 && $this->isFueled() && !$ingredient->isNull()){
                $this->setBrewTime(BrewingStandTile::MAX_BREW_TIME);
                $this->inventory->sendBrewTimeData(null, $this->getBrewTime());
            }
        }else{
            if($potionCount <= 0 || $ingredient->isNull()){
                $this->stopBrewing();
            }
        }
    }

    public function stopBrewing(): void{
        $this->setBrewTime(0);
        $this->inventory->sendBrewTimeData(null, $this->getBrewTime());
    }

    public function onUpdate(): bool{
        if($this->closed){
            return false;
        }
        if($this->removeFuel && !$this->inventory->getFuel()->isNull()){
            $this->inventory->decreaseFuel();
            $this->removeFuel = false;
        }
        if($this->isBrewing()){
            $this->brewTime--;

            if($this->brewTime <= 0){
                $ingredient = $this->inventory->getIngredient();

                for($i = BrewingStandInventory::FIRST_POTION_SLOT; $i <= BrewingStandInventory::THIRD_POTION_SLOT; $i++){
                    $potion = $this->inventory->getItem($i);

                    if($this->inventory->isPotion($potion, $ingredient)){
                        $inventoryManager = VanillaX::getInstance()->getInventoryManager();

                        if(($output = $inventoryManager->getBrewingOutput($potion, $ingredient)) === null){
                            $output = $inventoryManager->getBrewingContainerOutput($potion, $ingredient);
                        }
                        if($output !== null){
                            $this->inventory->setItem($i, $output);
                        }
                    }
                }
                $this->inventory->decreaseIngredient();
                $this->setFuelAmount($this->fuelAmount - 1, true);
                $this->getLevel()->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_POTION_BREWED);

                if($this->fuelAmount <= 0 && !$this->inventory->getFuel()->isNull()){
                    $this->inventory->decreaseFuel();
                    $this->resetFuel();
                }
            }
        }
        return true;
    }

    public function close(): void{
        foreach($this->inventory->getContents() as $item){
            $this->getLevel()->dropItem($this, $item);
        }
        $this->inventory->clearAll();
        parent::close();
    }

    protected function readSaveData(CompoundTag $nbt): void{
        $this->inventory = new BrewingStandInventory($this);
        $this->loadItems($nbt);
        $this->loadName($nbt);
        if($nbt->hasTag($tag = self::TAG_BREW_TIME)){
            $this->brewTime = $nbt->getInt($tag);
        }
        if($nbt->hasTag($tag = self::TAG_FUEL_AMOUNT)){
            $this->fuelAmount = $nbt->getInt($tag);
        }
        if($nbt->hasTag($tag = self::TAG_FUEL_TOTAL)){
            $this->fuelTotal = $nbt->getInt($tag);
        }
        $this->scheduleUpdate();
    }

    protected function writeSaveData(CompoundTag $nbt): void{
        $this->saveItems($nbt);
        $this->saveName($nbt);
        $nbt->setInt(self::TAG_BREW_TIME, $this->brewTime);
        $nbt->setInt(self::TAG_FUEL_AMOUNT, $this->fuelAmount);
        $nbt->setInt(self::TAG_FUEL_TOTAL, $this->fuelTotal);
    }

    protected function addAdditionalSpawnData(CompoundTag $nbt): void{
    }
}