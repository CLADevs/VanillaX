<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class PhantomEntity extends VanillaEntity{

    const NETWORK_ID = self::PHANTOM;

    public $width = 0.9;
    public $height = 0.5;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Phantom";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $phantom_membrane = ItemFactory::get(ItemIds::PHANTOM_MEMBRANE, 0, 1);
        ItemHelper::applySetCount($phantom_membrane, 0, 1);
        ItemHelper::applyLootingEnchant($this, $phantom_membrane);
        return [$phantom_membrane];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }

    public function getClassification(): int{
        return EntityClassification::UNDEAD;
    }
}