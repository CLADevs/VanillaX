<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EvocationIllagerEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::EVOCATION_ILLAGER;

    public float $width = 0.6;
    public float $height = 1.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(24);
    }

    public function getName(): string{
        return "Evocation Illager";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $emerald = ItemFactory::getInstance()->get(ItemIds::EMERALD, 0, 1);
        ItemHelper::applySetCount($emerald, 0, 1);
        ItemHelper::applyLootingEnchant($this, $emerald);
         
        $totem = ItemFactory::getInstance()->get(ItemIds::TOTEM, 0, 1);
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