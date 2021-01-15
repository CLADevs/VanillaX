<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\utils\EntityAgeable;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class BeeEntity extends LivingEntity{

    public $width = 0.55;
    public $height = 0.5;

    const NETWORK_ID = self::BEE;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.275, 0.25], [0.55, 0.5]);
        $this->ageable->setGrowthItems([
            ItemIds::RED_FLOWER,
            ItemIds::YELLOW_FLOWER,
            //TODO WITHER ROSE
            ItemIds::DOUBLE_PLANT, //Sunflower
            ItemFactory::get(ItemIds::DOUBLE_PLANT, 1), //Lilac
            ItemFactory::get(ItemIds::DOUBLE_PLANT, 4), //Rose Bush
            ItemFactory::get(ItemIds::DOUBLE_PLANT, 5), //Peony
        ]);
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Bee";
    }
}