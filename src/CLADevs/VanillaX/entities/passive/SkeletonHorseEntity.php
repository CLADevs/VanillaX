<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\interferces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class SkeletonHorseEntity extends VanillaEntity{

    const NETWORK_ID = self::SKELETON_HORSE;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(15);
    }

    public function getName(): string{
        return "Skeleton Horse";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $bone = ItemFactory::get(ItemIds::BONE, 0, 1);
        ItemHelper::applySetCount($bone, 0, 2);
        ItemHelper::applyLootingEnchant($this, $bone);
        return [$bone];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::UNDEAD;
    }
}