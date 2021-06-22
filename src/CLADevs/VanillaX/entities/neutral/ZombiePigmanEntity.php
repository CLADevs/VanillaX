<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class ZombiePigmanEntity extends VanillaEntity{

    const NETWORK_ID = self::ZOMBIE_PIGMAN;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Zombie Pigman";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $rotten_flesh = ItemFactory::get(ItemIds::ROTTEN_FLESH, 0, 1);
        ItemHelper::applySetCount($rotten_flesh, 0, 1);
        ItemHelper::applyLootingEnchant($this, $rotten_flesh);
         
        $gold_nugget = ItemFactory::get(ItemIds::GOLD_NUGGET, 0, 1);
        ItemHelper::applySetCount($gold_nugget, 0, 1);
        ItemHelper::applyLootingEnchant($this, $gold_nugget);
        return [$rotten_flesh, $gold_nugget, ItemFactory::get(ItemIds::GOLD_INGOT, 0, 1)];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 + (count($this->getArmorInventory()->getContents()) * mt_rand(1,3)) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::UNDEAD;
    }
}