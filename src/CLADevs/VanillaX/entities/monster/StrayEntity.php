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

class StrayEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::STRAY;

    public float $width = 0.6;
    public float $height = 1.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Stray";
    }

    public function getClassification(): int{
        return EntityClassification::UNDEAD;
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 + (count($this->getArmorInventory()->getContents()) * mt_rand(1, 3)) : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $arrow = ItemFactory::getInstance()->get(ItemIds::ARROW, 0, 1);
        ItemHelper::applySetCount($arrow, 0, 2);
        ItemHelper::applyLootingEnchant($this, $arrow);
         
        $bone = ItemFactory::getInstance()->get(ItemIds::BONE, 0, 1);
        ItemHelper::applySetCount($bone, 0, 2);
        ItemHelper::applyLootingEnchant($this, $bone);
         
        $arrow = ItemFactory::getInstance()->get(ItemIds::ARROW, 0, 1);
        ItemHelper::applySetCount($arrow, 0, 1);
        ItemHelper::applyLootingEnchant($this, $arrow);
        return [$arrow, $bone, $arrow];
    }
}