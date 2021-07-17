<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class RabbitEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::RABBIT;

    public float $width = 0.67;
    public float $height = 0.67;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(3);
    }

    public function getName(): string{
        return "Rabbit";
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $rabbit_hide = ItemFactory::getInstance()->get(ItemIds::RABBIT_HIDE, 0, 1);
        ItemHelper::applySetCount($rabbit_hide, 0, 1);
        ItemHelper::applyLootingEnchant($this, $rabbit_hide);
         
        $rabbit = ItemFactory::getInstance()->get(ItemIds::RAW_RABBIT, 0, 1);
        ItemHelper::applySetCount($rabbit, 0, 1);
        ItemHelper::applyFurnaceSmelt($rabbit);
        return [$rabbit_hide, $rabbit, ItemFactory::getInstance()->get(ItemIds::RABBIT_FOOT, 0, 1)];
    }
}