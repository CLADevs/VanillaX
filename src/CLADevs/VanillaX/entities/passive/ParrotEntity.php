<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class ParrotEntity extends VanillaEntity{

    const NETWORK_ID = self::PARROT;

    public $width = 0.5;
    public $height = 1;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Parrot";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $feather = ItemFactory::get(ItemIds::FEATHER, 0, 1);
        ItemHelper::applySetCount($feather, 1, 2);
        return [$feather];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}