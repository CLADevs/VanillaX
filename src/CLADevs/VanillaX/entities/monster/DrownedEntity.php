<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class DrownedEntity extends VanillaEntity{

    const NETWORK_ID = self::DROWNED;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Drowned";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        //TODO Copper Ingot & other items
        $rotten_flesh = ItemFactory::get(ItemIds::ROTTEN_FLESH, 0, 1);
        ItemHelper::applySetCount($rotten_flesh, 0, 2);
        ItemHelper::applyLootingEnchant($this, $rotten_flesh);
        return [$rotten_flesh];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 + (count($this->getArmorInventory()->getContents()) * mt_rand(1,3)) : 0;
    }

    public function getClassification(): int{
        return EntityClassification::UNDEAD;
    }
}