<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class StriderEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::STRIDER;

    public float $width = 0.9;
    public float $height = 1.7;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Strider";
    }

    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $string = ItemFactory::getInstance()->get(ItemIds::STRING, 0, 1);
        ItemHelper::applySetCount($string, 2, 5);
        return [$string];
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
}