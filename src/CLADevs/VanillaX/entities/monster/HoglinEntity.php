<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class HoglinEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::HOGLIN;

    public float $width = 0.6;
    public float $height = 1.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Hoglin";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $porkchop = ItemFactory::getInstance()->get(ItemIds::RAW_PORKCHOP, 0, 1);
        ItemHelper::applySetCount($porkchop, 2, 4);
        ItemHelper::applyFurnaceSmelt($porkchop);
        ItemHelper::applyLootingEnchant($this, $porkchop);
         
        $leather = ItemFactory::getInstance()->get(ItemIds::LEATHER, 0, 1);
        ItemHelper::applySetCount($leather, 0, 1);
        ItemHelper::applyLootingEnchant($this, $leather);
        return [$porkchop, $leather];
    }
}