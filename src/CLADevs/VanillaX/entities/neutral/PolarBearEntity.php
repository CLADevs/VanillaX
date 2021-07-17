<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class PolarBearEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::POLAR_BEAR;

    public float $width = 1.3;
    public float $height = 1.4;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Polar Bear";
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $fish = ItemFactory::getInstance()->get(ItemIds::RAW_FISH, 0, 1);
        ItemHelper::applySetCount($fish, 0, 2);
        ItemHelper::applyLootingEnchant($this, $fish);
         
        $salmon = ItemFactory::getInstance()->get(ItemIds::SALMON, 0, 1);
        ItemHelper::applySetCount($salmon, 0, 2);
        ItemHelper::applyLootingEnchant($this, $salmon);
        return [$fish, $salmon];
    }
}