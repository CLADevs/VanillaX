<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class ShulkerEntity extends VanillaEntity{

    const NETWORK_ID = self::SHULKER;

    public $width = 0.6;
    public $height = 1.9;

    protected function initEntity(): void{
        parent::initEntity();
        $this->setMaxHealth(30);
    }

    public function getName(): string{
        return "Shulker";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $shulker_shell = ItemFactory::get(ItemIds::SHULKER_SHELL, 0, 1);
        ItemHelper::applySetCount($shulker_shell, 0, 1);
        ItemHelper::applyLootingEnchant($this, $shulker_shell);
        return [$shulker_shell];
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? 5 : 0;
    }
}