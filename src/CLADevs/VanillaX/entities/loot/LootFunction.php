<?php

namespace CLADevs\VanillaX\entities\loot;

use pocketmine\entity\Entity;
use pocketmine\item\Item;

abstract class LootFunction{

    public function apply(Item $item): void{}
    public function customApply(Entity $killer, Item $item): void{}
}