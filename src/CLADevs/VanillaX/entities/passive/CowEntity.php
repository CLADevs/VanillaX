<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class CowEntity extends VanillaEntity{

    const NETWORK_ID = self::COW;

    public $width = 0.9;
    public $height = 1.3;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Cow";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $leather = ItemFactory::get(ItemIds::LEATHER, 0, 1);
        ItemHelper::applySetCount($leather, 0, 2);
        ItemHelper::applyLootingEnchant($this, $leather);
         
        $beef = ItemFactory::get(ItemIds::RAW_BEEF, 0, 1);
        ItemHelper::applySetCount($beef, 1, 3);
        if($this->isOnFire()) ItemHelper::applyFurnaceSmelt($beef);
        ItemHelper::applyLootingEnchant($this, $beef);
        return [$leather, $beef];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}