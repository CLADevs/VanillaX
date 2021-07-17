<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class MagmaCubeEntity extends VanillaEntity{

    const NETWORK_ID = EntityIds::MAGMA_CUBE;

    public float $width = 0.6;
    public float $height = 1.9;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        
    }

    public function getName(): string{
        return "Magma_Cube";
    }
    
    public function getXpDropAmount(): int{
        return $this->getLastHitByPlayer() ? self::NETWORK_ID : 0;
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $magma_cream = ItemFactory::getInstance()->get(ItemIds::MAGMA_CREAM, 0, 1);
        ItemHelper::applySetCount($magma_cream, 0, 1);
        ItemHelper::applyLootingEnchant($this, $magma_cream);
        return [$magma_cream];
    }
}