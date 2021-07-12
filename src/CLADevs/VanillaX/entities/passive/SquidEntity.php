<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class SquidEntity extends VanillaEntity{

    const NETWORK_ID = self::SQUID;

    public $width = 0.95;
    public $height = 0.95;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Squid";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $dye = ItemFactory::getInstance()->get(ItemIds::DYE, 0, 1);
        ItemHelper::applySetCount($dye, 1, 3);
        ItemHelper::applySetData($dye, 0);
        ItemHelper::applyLootingEnchant($this, $dye);
        return [$dye];
    }
    
    public function getXpDropAmount(): int{
        return !$this->isBaby() && $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }
}