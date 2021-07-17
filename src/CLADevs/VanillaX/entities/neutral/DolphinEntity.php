<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\utils\interfaces\EntityClassification;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class DolphinEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::DOLPHIN;

    public float $width = 0.9;
    public float $height = 0.6;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Dolphin";
    }

    public function getClassification(): int{
        return EntityClassification::AQUATIC;
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $fish = ItemFactory::getInstance()->get(ItemIds::RAW_FISH, 0, 1);
        ItemHelper::applySetCount($fish, 0, 1);
        ItemHelper::applyLootingEnchant($this, $fish);
        ItemHelper::applyFurnaceSmelt($fish);
        return [$fish];
    }
}