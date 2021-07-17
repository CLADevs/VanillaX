<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class CatEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::CAT;

    public float $width = 0.6;
    public float $height = 0.7;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        
    }

    public function getName(): string{
        return "Cat";
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $string = ItemFactory::getInstance()->get(ItemIds::STRING, 0, 1);
        ItemHelper::applySetCount($string, 0, 2);
        return [$string];
    }
}