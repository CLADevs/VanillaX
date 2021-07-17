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

class PhantomEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::PHANTOM;

    public float $width = 0.9;
    public float $height = 0.5;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(20);
    }

    public function getName(): string{
        return "Phantom";
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
        $phantom_membrane = ItemFactory::getInstance()->get(ItemIds::PHANTOM_MEMBRANE, 0, 1);
        ItemHelper::applySetCount($phantom_membrane, 0, 1);
        ItemHelper::applyLootingEnchant($this, $phantom_membrane);
        return [$phantom_membrane];
    }
}