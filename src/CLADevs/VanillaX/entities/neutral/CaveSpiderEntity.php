<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\utils\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class CaveSpiderEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::CAVE_SPIDER;

    public float $width = 0.7;
    public float $height = 0.5;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(12);
    }

    public function getName(): string{
        return "Cave Spider";
    }

    public function getClassification(): int{
        return EntityClassification::ARTHROPODS;
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $string = ItemFactory::getInstance()->get(ItemIds::STRING, 0, 1);
        ItemHelper::applySetCount($string, 0, 2);
        ItemHelper::applyLootingEnchant($this, $string);
         
        $spider_eye = ItemFactory::getInstance()->get(ItemIds::SPIDER_EYE, 0, 1);
        ItemHelper::applySetCount($spider_eye, 0, 1);
        ItemHelper::applyLootingEnchant($this, $spider_eye);
        return [$string, $spider_eye];
    }
}