<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class GhastEntity extends VanillaEntity{

    const NETWORK_ID = self::GHAST;

    public $width = 4;
    public $height = 4;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Ghast";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $ghast_tear = ItemFactory::getInstance()->get(ItemIds::GHAST_TEAR, 0, 1);
        ItemHelper::applySetCount($ghast_tear, 0, 1);
        ItemHelper::applyLootingEnchant($this, $ghast_tear);
         
        $gunpowder = ItemFactory::getInstance()->get(ItemIds::GUNPOWDER, 0, 1);
        ItemHelper::applySetCount($gunpowder, 0, 2);
        ItemHelper::applyLootingEnchant($this, $gunpowder);
        return [$ghast_tear, $gunpowder];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 + (count($this->getArmorInventory()->getContents()) * mt_rand(1,3)) : 0;
    }
}