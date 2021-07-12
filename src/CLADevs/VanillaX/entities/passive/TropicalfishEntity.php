<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use CLADevs\VanillaX\entities\utils\ItemHelper;

class TropicalfishEntity extends VanillaEntity{

    const NETWORK_ID = self::TROPICAL_FISH;

    public $width = 0.4;
    public $height = 0.4;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Tropical Fish";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $bone = ItemFactory::getInstance()->get(ItemIds::BONE, 0, 1);
        ItemHelper::applyLootingEnchant($this, $bone);
        return [ItemFactory::getInstance()->get(ItemIds::CLOWNFISH, 0, 1), $bone];
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }
}