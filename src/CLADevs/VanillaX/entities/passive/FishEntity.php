<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class FishEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::COD;

    public float $width = 0.6;
    public float $height = 0.3;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Fish";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $fish = ItemFactory::getInstance()->get(ItemIds::RAW_FISH, 0, 1);
        if($this->isOnFire()) ItemHelper::applyFurnaceSmelt($fish);
         
        $bone = ItemFactory::getInstance()->get(ItemIds::BONE, 0, 1);
        ItemHelper::applyLootingEnchant($this, $bone);
        return [$fish, $bone];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }
}