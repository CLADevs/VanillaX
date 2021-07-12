<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class HoglinEntity extends VanillaEntity{

    const NETWORK_ID = self::HOGLIN;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Hoglin";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $porkchop = ItemFactory::getInstance()->get(ItemIds::RAW_PORKCHOP, 0, 1);
        ItemHelper::applySetCount($porkchop, 2, 4);
        ItemHelper::applyFurnaceSmelt($porkchop);
        ItemHelper::applyLootingEnchant($this, $porkchop);
         
        $leather = ItemFactory::getInstance()->get(ItemIds::LEATHER, 0, 1);
        ItemHelper::applySetCount($leather, 0, 1);
        ItemHelper::applyLootingEnchant($this, $leather);
        return [$porkchop, $leather];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }
}