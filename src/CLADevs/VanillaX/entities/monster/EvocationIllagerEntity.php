<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interferces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class EvocationIllagerEntity extends VanillaEntity{

    const NETWORK_ID = self::EVOCATION_ILLAGER;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(24);
    }

    public function getName(): string{
        return "Evocation Illager";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $emerald = ItemFactory::get(ItemIds::EMERALD, 0, 1);
        ItemHelper::applySetCount($emerald, 0, 1);
        ItemHelper::applyLootingEnchant($this, $emerald);
         
        $totem = ItemFactory::get(ItemIds::TOTEM, 0, 1);
        ItemHelper::applySetCount($totem, 1, 1);
        return [$emerald, $totem];
    }
    
    public function getXpDropAmount(): int{
        return 10;
    }

    public function getClassification(): int{
        return EntityClassification::ILLAGERS;
    }
}