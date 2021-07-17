<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ShulkerEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::SHULKER;

    public float $width = 0.6;
    public float $height = 1.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Shulker";
    }

    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $shulker_shell = ItemFactory::getInstance()->get(ItemIds::SHULKER_SHELL, 0, 1);
        ItemHelper::applySetCount($shulker_shell, 0, 1);
        ItemHelper::applyLootingEnchant($this, $shulker_shell);
        return [$shulker_shell];
    }
}