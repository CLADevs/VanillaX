<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class RavagerEntity extends VanillaEntity{

    const NETWORK_ID = self::RAVAGER;

    public $width = 1.9;
    public $height = 1.2;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(100);
    }

    public function getName(): string{
        return "Ravager";
    }

    /**
     * @return Item[]
     */
    public function getDrops(): array{
        return [ItemFactory::get(ItemIds::SADDLE, 0, 1)];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 20 : 0;
    }

    public function getClassification(): int{
        return EntityClassification::ILLAGERS;
    }
}