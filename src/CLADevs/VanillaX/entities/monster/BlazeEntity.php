<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class BlazeEntity extends VanillaEntity{

    const NETWORK_ID = self::BLAZE;

    public $width = 0.5;
    public $height = 1.8;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Blaze";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $blaze_rod = ItemFactory::get(ItemIds::BLAZE_ROD, 0, 1);
        ItemHelper::applySetCount($blaze_rod, 0, 1);
        ItemHelper::applyLootingEnchant($this, $blaze_rod);
        return [$blaze_rod];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 10 : 0;
    }
}