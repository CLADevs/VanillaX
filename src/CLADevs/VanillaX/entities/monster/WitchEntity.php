<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class WitchEntity extends VanillaEntity{

    const NETWORK_ID = self::WITCH;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(26);
    }

    public function getName(): string{
        return "Witch";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $glowstone_dust = ItemFactory::get(ItemIds::GLOWSTONE_DUST, 0, 1);
        ItemHelper::applySetCount($glowstone_dust, 0, 2);
        ItemHelper::applyLootingEnchant($this, $glowstone_dust);
         
        $sugar = ItemFactory::get(ItemIds::SUGAR, 0, 1);
        ItemHelper::applySetCount($sugar, 0, 2);
        ItemHelper::applyLootingEnchant($this, $sugar);
         
        $redstone = ItemFactory::get(ItemIds::REDSTONE_DUST, 0, 1);
        ItemHelper::applySetCount($redstone, 0, 2);
        ItemHelper::applyLootingEnchant($this, $redstone);
         
        $spider_eye = ItemFactory::get(ItemIds::SPIDER_EYE, 0, 1);
        ItemHelper::applySetCount($spider_eye, 0, 2);
        ItemHelper::applyLootingEnchant($this, $spider_eye);
         
        $glass_bottle = ItemFactory::get(ItemIds::GLASS_BOTTLE, 0, 1);
        ItemHelper::applySetCount($glass_bottle, 0, 2);
        ItemHelper::applyLootingEnchant($this, $glass_bottle);
         
        $gunpowder = ItemFactory::get(ItemIds::GUNPOWDER, 0, 1);
        ItemHelper::applySetCount($gunpowder, 0, 2);
        ItemHelper::applyLootingEnchant($this, $gunpowder);
         
        $stick = ItemFactory::get(ItemIds::STICK, 0, 1);
        ItemHelper::applySetCount($stick, 0, 2);
        ItemHelper::applyLootingEnchant($this, $stick);
        return [$glowstone_dust, $sugar, $redstone, $spider_eye, $glass_bottle, $gunpowder, $stick];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? ($this->isBaby() ? 12 : 5) + (mt_rand(1,3)) : 0;
    }
}