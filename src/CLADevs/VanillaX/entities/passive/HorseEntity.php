<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\ItemHelper;
use CLADevs\VanillaX\entities\VanillaEntity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class HorseEntity extends VanillaEntity{

    const NETWORK_ID = self::HORSE;

    public $width = 1.4;
    public $height = 1.6;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setRangeHealth([15, 30]);
    }

    public function getName(): string{
        return "Horse";
    }

    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $leather = ItemFactory::getInstance()->get(ItemIds::LEATHER, 0, 1);
        ItemHelper::applySetCount($leather, 0, 2);
        ItemHelper::applyLootingEnchant($this, $leather);
        return [$leather];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1,3) : 0;
    }
}