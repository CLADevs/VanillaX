<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\utils\interferces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class CaveSpiderEntity extends VanillaEntity{

    const NETWORK_ID = self::CAVE_SPIDER;

    public $width = 0.7;
    public $height = 0.5;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(12);
    }

    public function getName(): string{
        return "Cave Spider";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $string = ItemFactory::get(ItemIds::STRING, 0, 1);
        ItemHelper::applySetCount($string, 0, 2);
        ItemHelper::applyLootingEnchant($this, $string);
         
        $spider_eye = ItemFactory::get(ItemIds::SPIDER_EYE, 0, 1);
        ItemHelper::applySetCount($spider_eye, 0, 1);
        ItemHelper::applyLootingEnchant($this, $spider_eye);
        return [$string, $spider_eye];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }

    public function getClassification(): int{
        return EntityClassification::ARTHROPODS;
    }
}