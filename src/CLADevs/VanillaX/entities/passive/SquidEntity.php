<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SquidEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::SQUID;

    public float $width = 0.95;
    public float $height = 0.95;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Squid";
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }

    public function getXpDropAmount(): int{
        return !$this->isBaby() && $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $dye = ItemFactory::getInstance()->get(ItemIds::DYE, 0, 1);
        ItemHelper::applySetCount($dye, 1, 3);
        ItemHelper::applySetData($dye, 0);
        ItemHelper::applyLootingEnchant($this, $dye);
        return [$dye];
    }
}