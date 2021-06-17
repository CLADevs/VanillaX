<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class IronGolemEntity extends VanillaEntity{

    const NETWORK_ID = self::IRON_GOLEM;

    public $width = 1.4;
    public $height = 2.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(100);
    }

    public function getName(): string{
        return "Iron Golem";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $red_flower = ItemFactory::get(ItemIds::RED_FLOWER, 0, 1);
        ItemHelper::applySetCount($red_flower, 0, 2);
         
        $iron_ingot = ItemFactory::get(ItemIds::IRON_INGOT, 0, 1);
        ItemHelper::applySetCount($iron_ingot, 3, 5);
        return [$red_flower, $iron_ingot];
    }
}