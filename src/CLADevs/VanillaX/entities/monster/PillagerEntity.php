<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;

class PillagerEntity extends VanillaEntity{

    const NETWORK_ID = self::LEGACY_ID_MAP_BC[self::PILLAGER];

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(24);
    }

    public function getName(): string{
        return "Pillager";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $arrow = ItemFactory::getInstance()->get(ItemIds::ARROW, 0, 1);
        ItemHelper::applySetCount($arrow, 0, 2);
        ItemHelper::applyLootingEnchant($this, $arrow);
        return [$arrow];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? ($this->isBaby() ? 12 : 5) + (mt_rand(1, 3)) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::ILLAGERS;
    }
}