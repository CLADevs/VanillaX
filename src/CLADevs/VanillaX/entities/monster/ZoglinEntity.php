<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ZoglinEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::ZOGLIN;

    public float $width = 0.6;
    public float $height = 1.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Zoglin";
    }

    public function getClassification(): int{
        return EntityClassification::UNDEAD;
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $rotten_flesh = ItemFactory::getInstance()->get(ItemIds::ROTTEN_FLESH, 0, 1);
        ItemHelper::applySetCount($rotten_flesh, 1, 3);
        ItemHelper::applyLootingEnchant($this, $rotten_flesh);
        return [$rotten_flesh];
    }
}