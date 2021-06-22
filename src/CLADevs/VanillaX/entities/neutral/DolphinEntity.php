<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class DolphinEntity extends VanillaEntity{

    const NETWORK_ID = self::DOLPHIN;

    public $width = 0.9;
    public $height = 0.6;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Dolphin";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $fish = ItemFactory::get(ItemIds::RAW_FISH, 0, 1);
        ItemHelper::applySetCount($fish, 0, 1);
        ItemHelper::applyLootingEnchant($this, $fish);
        if($this->isOnFire()) ItemHelper::applyFurnaceSmelt($fish);
        return [$fish];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }
}