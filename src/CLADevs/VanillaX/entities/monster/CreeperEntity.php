<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class CreeperEntity extends VanillaEntity{

    const NETWORK_ID = self::CREEPER;

    public $width = 0.6;
    public $height = 1.8;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function isCharged(): bool{
        return $this->getGenericFlag(self::DATA_FLAG_POWERED);
    }

    public function getName(): string{
        return "Creeper";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        //TODO, killed by skeleton drops music disc
        $gunpowder = ItemFactory::get(ItemIds::GUNPOWDER, 0, 1);
        ItemHelper::applySetCount($gunpowder, 0, 2);
        ItemHelper::applyLootingEnchant($this, $gunpowder);
        return [$gunpowder];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }
}