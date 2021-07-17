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

class WitherSkeletonEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::WITHER_SKELETON;

    public float $width = 0.72;
    public float $height = 2.01;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Wither_Skeleton";
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
        $coal = ItemFactory::getInstance()->get(ItemIds::COAL, 0, 1);
        ItemHelper::applySetCount($coal, 0, 1);
        ItemHelper::applyLootingEnchant($this, $coal);
         
        $bone = ItemFactory::getInstance()->get(ItemIds::BONE, 0, 1);
        ItemHelper::applySetCount($bone, 0, 2);
        ItemHelper::applyLootingEnchant($this, $bone);
         
        $skull = ItemFactory::getInstance()->get(ItemIds::SKULL, 0, 1);
        ItemHelper::applySetData($skull, 1);
        return [$coal, $bone, $skull];
    }
}