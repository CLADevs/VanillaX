<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class EndermanEntity extends VanillaEntity{

    const NETWORK_ID = self::ENDERMAN;

    public $width = 0.6;
    public $height = 2.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Enderman";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $ender_pearl = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 1);
        ItemHelper::applySetCount($ender_pearl, 0, 1);
        ItemHelper::applyLootingEnchant($this, $ender_pearl);
        return [$ender_pearl];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }
}