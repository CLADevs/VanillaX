<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class ChickenEntity extends VanillaEntity{

    const NETWORK_ID = self::CHICKEN;

    public $width = 0.6;
    public $height = 0.8;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(4);
    }

    public function getName(): string{
        return "Chicken";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $feather = ItemFactory::get(ItemIds::FEATHER, 0, 1);
        ItemHelper::applySetCount($feather, 0, 2);
        ItemHelper::applyLootingEnchant($this, $feather);
         
        $chicken = ItemFactory::get(ItemIds::RAW_CHICKEN, 0, 1);
        if($this->isOnFire()) ItemHelper::applyFurnaceSmelt($chicken);
        ItemHelper::applyLootingEnchant($this, $chicken);
        return [$feather, $chicken];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}