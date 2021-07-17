<?php

namespace CLADevs\VanillaX\entities\neutral;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EndermanEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::ENDERMAN;

    public float $width = 0.6;
    public float $height = 2.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(40);
    }

    public function getName(): string{
        return "Enderman";
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $ender_pearl = ItemFactory::getInstance()->get(ItemIds::ENDER_PEARL, 0, 1);
        ItemHelper::applySetCount($ender_pearl, 0, 1);
        ItemHelper::applyLootingEnchant($this, $ender_pearl);
        return [$ender_pearl];
    }
}