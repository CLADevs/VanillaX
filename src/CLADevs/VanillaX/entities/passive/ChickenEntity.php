<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ChickenEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::CHICKEN;

    public float $width = 0.6;
    public float $height = 0.8;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(4);
    }

    public function getName(): string{
        return "Chicken";
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $feather = ItemFactory::getInstance()->get(ItemIds::FEATHER, 0, 1);
        ItemHelper::applySetCount($feather, 0, 2);
        ItemHelper::applyLootingEnchant($this, $feather);
         
        $chicken = ItemFactory::getInstance()->get(ItemIds::RAW_CHICKEN, 0, 1);
        ItemHelper::applyFurnaceSmelt($chicken);
        ItemHelper::applyLootingEnchant($this, $chicken);
        return [$feather, $chicken];
    }
}