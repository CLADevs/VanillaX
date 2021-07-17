<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;

class GlowSquidEntity extends VanillaEntity{

    const NETWORK_ID = self::LEGACY_ID_MAP_BC[self::GLOW_SQUID];

    public float $width = 0.95;
    public float $height = 0.95;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(10);
    }

    public function getName(): string{
        return "Glow Squid";
    }

    public function getXpDropAmount(): int{
        return !$this->isBaby() && $this->getLastHitByPlayer() ? mt_rand(1, 3) : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $glow_ink_sac = ItemFactory::getInstance()->get(ItemIds::AIR, 0, 1);
        ItemHelper::applySetCount($glow_ink_sac, 1, 3);
        ItemHelper::applySetData($glow_ink_sac, 0);
        ItemHelper::applyLootingEnchant($this, $glow_ink_sac);
        return [$glow_ink_sac];
    }
}