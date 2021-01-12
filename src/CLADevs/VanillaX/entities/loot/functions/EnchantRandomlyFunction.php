<?php

namespace CLADevs\VanillaX\entities\loot\functions;

use CLADevs\VanillaX\enchantments\EnchantmentManager;
use CLADevs\VanillaX\entities\loot\LootFunction;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;

class EnchantRandomlyFunction extends LootFunction{

    const NAME = "enchant_randomly";

    private bool $treasure;

    public function __construct(bool $treasure){
        $this->treasure = $treasure;
    }

    public function apply(Item $item): void{
        if($this->treasure){
            $enchantments = EnchantmentManager::$treasure;
        }else{
            $enchantments = EnchantmentManager::getAllEnchantments(false);
        }
        $enchant = Enchantment::getEnchantment($enchantments[array_rand($enchantments)]);
        $item->addEnchantment(new EnchantmentInstance($enchant, mt_rand(1, $enchant->getMaxLevel())));
    }
}