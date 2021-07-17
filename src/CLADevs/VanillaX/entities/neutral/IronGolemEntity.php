<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class IronGolemEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::IRON_GOLEM;

    public float $width = 1.4;
    public float $height = 2.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(100);
    }

    public function getName(): string{
        return "Iron_Golem";
    }

    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $red_flower = ItemFactory::getInstance()->get(ItemIds::RED_FLOWER, 0, 1);
        ItemHelper::applySetCount($red_flower, 0, 2);
         
        $iron_ingot = ItemFactory::getInstance()->get(ItemIds::IRON_INGOT, 0, 1);
        ItemHelper::applySetCount($iron_ingot, 3, 5);
        return [$red_flower, $iron_ingot];
    }
}