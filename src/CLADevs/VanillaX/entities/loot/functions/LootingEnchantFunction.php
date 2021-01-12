<?php

namespace CLADevs\VanillaX\entities\loot\functions;

use CLADevs\VanillaX\entities\loot\LootFunction;
use pocketmine\entity\Entity;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\Item;
use pocketmine\Player;

class LootingEnchantFunction extends LootFunction{

    const NAME = "looting_enchant";

    public function customApply(Entity $killer, Item $item): void{
        if($killer instanceof Player && ($level = $killer->getInventory()->getItemInHand()->getEnchantmentLevel(Enchantment::LOOTING)) > 0){
            $item->setCount($item->getCount() + mt_rand(0, $level));
        }
    }
}