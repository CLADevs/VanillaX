<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SheepEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::SHEEP;

    public float $width = 0.9;
    public float $height = 1.3;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(8);
    }

    public function getName(): string{
        return "Sheep";
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $wool = ItemFactory::getInstance()->get(ItemIds::WOOL, 0, 1);
         
        $muttonraw = ItemFactory::getInstance()->get(ItemIds::RAW_MUTTON, 0, 1);
        ItemHelper::applySetCount($muttonraw, 1, 2);
        ItemHelper::applyFurnaceSmelt($muttonraw);
        ItemHelper::applyLootingEnchant($this, $muttonraw);
        return [$wool, $muttonraw];
    }
}