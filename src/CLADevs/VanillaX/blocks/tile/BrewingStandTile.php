<?php

namespace CLADevs\VanillaX\blocks\tile;

use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\inventories\types\BrewingStandInventory;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Container;
use pocketmine\block\tile\ContainerTrait;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\NameableTrait;
use pocketmine\block\tile\Spawnable;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

class BrewingStandTile extends Spawnable implements Container, Nameable{
    use ContainerTrait, NameableTrait;

    const TILE_ID = TileVanilla::BREWING_STAND;
    const TILE_BLOCK = BlockLegacyIds::BREWING_STAND_BLOCK;

    const TAG_BREW_TIME = "BrewTime";
    const TAG_FUEL_AMOUNT = "FuelAmount";
    const TAG_FUEL_TOTAL = "FuelTotal";

    const MAX_BREW_TIME = 400;

    private bool $removeFuel = false;

    private int $brewTime = 0;
    private int $fuelAmount = 0;
    private int $fuelTotal = 0;

    private BrewingStandInventory $inventory;

    public function getDefaultName(): string{
        return TileVanilla::BREWING_STAND;
    }

    public function getInventory(): BrewingStandInventory{
        return $this->inventory;
    }

    public function getRealInventory(): BrewingStandInventory{
        return $this->inventory;
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
                $this->getPos()->getWorld()->broadcastPacketToViewers($this->getPos(), LevelSoundEventPacket::create(LevelSoundEventPacket::SOUND_POTION_BREWED, $this->getPos()));

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
            $this->getPos()->getWorld()->dropItem($this->getPos(), $item);
        }
        $this->inventory->clearAll();
        parent::close();
    }

    public function readSaveData(CompoundTag $nbt): void{
        $this->inventory = new BrewingStandInventory($this);
        $this->loadItems($nbt);
        $this->loadName($nbt);
        if(($tag = $nbt->getTag(self::TAG_BREW_TIME)) !== null){
            $this->brewTime = $tag->getValue();
        }
        if(($tag = $nbt->getTag(self::TAG_FUEL_AMOUNT)) !== null){
            $this->fuelAmount = $tag->getValue();
        }
        if(($tag = $nbt->getTag(self::TAG_FUEL_TOTAL)) !== null){
            $this->fuelTotal = $tag->getValue();
        }
        //TODO repeating tick
        //$this->scheduleUpdate();
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