<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class BlazeEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::BLAZE;

    public float $width = 0.5;
    public float $height = 1.8;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Blaze";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 10 : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $blaze_rod = ItemFactory::getInstance()->get(ItemIds::BLAZE_ROD, 0, 1);
        ItemHelper::applySetCount($blaze_rod, 0, 1);
        ItemHelper::applyLootingEnchant($this, $blaze_rod);
        return [$blaze_rod];
    }
}