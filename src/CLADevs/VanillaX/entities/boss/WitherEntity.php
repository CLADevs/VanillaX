<?php

namespace CLADevs\VanillaX\entities\boss;

use CLADevs\VanillaX\entities\utils\interferces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class WitherEntity extends VanillaEntity{

    const NETWORK_ID = self::WITHER;

    public $width = 1;
    public $height = 3;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(600);
    }

    public function getName(): string{
        return "Wither";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        return [ItemFactory::get(ItemIds::NETHER_STAR, 0, 1)];
    }
    
    public function getXpDropAmount(): int{
        return 50;
    }

    public function getClassification(): int{
        return EntityClassification::UNDEAD;
    }
}