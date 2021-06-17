<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\utils\ItemHelper;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class MagmaCubeEntity extends SlimeEntity{

    const NETWORK_ID = self::MAGMA_CUBE;

    public function getName(): string{
        return "Magma Cube";
    }
 
    /**
     * @return Item[]
     */
    public function getDrops(): array{
        $magma_cream = ItemFactory::get(ItemIds::MAGMA_CREAM, 0, 1);
        ItemHelper::applySetCount($magma_cream, 0, 1);
        ItemHelper::applyLootingEnchant($this, $magma_cream);
        return [$magma_cream];
    }
}