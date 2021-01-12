<?php

namespace CLADevs\VanillaX\entities\loot\functions;

use CLADevs\VanillaX\entities\loot\LootFunction;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;

class SpecificEnchantsFunction extends LootFunction{

    const NAME = "specific_enchants";

    private array $enchants;

    public function __construct(array $enchants){
        $this->enchants = $enchants;
    }

    public function apply(Item $item): void{
        foreach($this->enchants as $i){
            $enchant = Enchantment::getEnchantment($i["id"]);

            if($enchant !== null){
                $level = mt_rand($i["level"][0], $i["level"][1]);
                $item->addEnchantment(new EnchantmentInstance($enchant, $level));
            }
        }
    }
}