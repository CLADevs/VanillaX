<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\ItemHelper;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class SnowGolemEntity extends VanillaEntity{

    const NETWORK_ID = self::SNOW_GOLEM;

    public $width = 0.4;
    public $height = 1.8;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(4);
    }

    public function getName(): string{
        return "Snow Golem";
    }

    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $snowball = ItemFactory::getInstance()->get(ItemIds::SNOWBALL, 0, 1);
        ItemHelper::applySetCount($snowball, 0, 15);
        return [$snowball];
    }
}