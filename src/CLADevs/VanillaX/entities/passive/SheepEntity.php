<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class SheepEntity extends VanillaEntity{

    const NETWORK_ID = self::SHEEP;

    public $width = 0.9;
    public $height = 1.3;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(8);
    }

    public function getName(): string{
        return "Sheep";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $wool = ItemFactory::getInstance()->get(ItemIds::WOOL, 0, 1);
         
        $muttonraw = ItemFactory::getInstance()->get(ItemIds::RAW_MUTTON, 0, 1);
        ItemHelper::applySetCount($muttonraw, 1, 2);
        if($this->isOnFire()) ItemHelper::applyFurnaceSmelt($muttonraw);
        ItemHelper::applyLootingEnchant($this, $muttonraw);
        return [$wool, $muttonraw];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}