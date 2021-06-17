<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class PigEntity extends VanillaEntity{

    const NETWORK_ID = self::PIG;

    public $width = 0.9;
    public $height = 0.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Pig";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $porkchop = ItemFactory::get(ItemIds::RAW_PORKCHOP, 0, 1);
        ItemHelper::applySetCount($porkchop, 1, 3);
        if($this->isOnFire()) ItemHelper::applyFurnaceSmelt($porkchop);
        ItemHelper::applyLootingEnchant($this, $porkchop);
        return [$porkchop];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}