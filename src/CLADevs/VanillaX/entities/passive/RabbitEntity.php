<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class RabbitEntity extends VanillaEntity{

    const NETWORK_ID = self::RABBIT;

    public $width = 0.67;
    public $height = 0.67;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(3);
    }

    public function getName(): string{
        return "Rabbit";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $rabbit_hide = ItemFactory::getInstance()->get(ItemIds::RABBIT_HIDE, 0, 1);
        ItemHelper::applySetCount($rabbit_hide, 0, 1);
        ItemHelper::applyLootingEnchant($this, $rabbit_hide);
         
        $rabbit = ItemFactory::getInstance()->get(ItemIds::RAW_RABBIT, 0, 1);
        ItemHelper::applySetCount($rabbit, 0, 1);
        if($this->isOnFire()) ItemHelper::applyFurnaceSmelt($rabbit);
        return [$rabbit_hide, $rabbit, ItemFactory::getInstance()->get(ItemIds::RABBIT_FOOT, 0, 1)];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}