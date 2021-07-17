<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class GhastEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::GHAST;

    public float $width = 4;
    public float $height = 4;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Ghast";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 + (count($this->getArmorInventory()->getContents()) * mt_rand(1, 3)) : 0;
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
}