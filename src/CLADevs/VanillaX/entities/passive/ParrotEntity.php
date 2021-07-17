<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ParrotEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::PARROT;

    public float $width = 0.5;
    public float $height = 1;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(6);
    }

    public function getName(): string{
        return "Parrot";
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $feather = ItemFactory::getInstance()->get(ItemIds::FEATHER, 0, 1);
        ItemHelper::applySetCount($feather, 1, 2);
        return [$feather];
    }
}