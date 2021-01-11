<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\item\ItemIds;

class DonkeyEntity extends LivingEntity{

    public $width = 1.4;
    public $height = 1.6;

    const NETWORK_ID = self::DONKEY;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.7, 0.8], [1.4, 1.6]);
        $this->ageable->setGrowthItems([
            ItemIds::WHEAT,
            ItemIds::SUGAR,
            ItemIds::HAY_BLOCK,
            ItemIds::APPLE,
            ItemIds::GOLDEN_CARROT,
            ItemIds::GOLDEN_APPLE,
            ItemIds::ENCHANTED_GOLDEN_APPLE
        ]);
        //TODO
//        $this->ageable->setItemsPoint([
//            ItemIds::WHEAT => 20,
//            ItemIds::SUGAR => 30,
//            ItemIds::HAY_BLOCK => 180,
//            ItemIds::APPLE => 60,
//            ItemIds::GOLDEN_CARROT => 60,
//            ItemIds::GOLDEN_APPLE => 240,
//            ItemIds::ENCHANTED_GOLDEN_APPLE => 240
//        ]);
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Donkey";
    }
}