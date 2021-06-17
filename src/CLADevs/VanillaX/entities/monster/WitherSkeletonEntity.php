<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interferces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class WitherSkeletonEntity extends VanillaEntity{

    const NETWORK_ID = self::WITHER_SKELETON;

    public $width = 0.72;
    public $height = 2.01;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Wither Skeleton";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $coal = ItemFactory::get(ItemIds::COAL, 0, 1);
        ItemHelper::applySetCount($coal, 0, 1);
        ItemHelper::applyLootingEnchant($this, $coal);
         
        $bone = ItemFactory::get(ItemIds::BONE, 0, 1);
        ItemHelper::applySetCount($bone, 0, 2);
        ItemHelper::applyLootingEnchant($this, $bone);
         
        $skull = ItemFactory::get(ItemIds::SKULL, 0, 1);
        ItemHelper::applySetData($skull, 1);
        return [$coal, $bone, $skull];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 + (count($this->getArmorInventory()->getContents()) * mt_rand(1,3)) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::UNDEAD;
    }
}