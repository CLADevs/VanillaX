<?php

namespace CLADevs\VanillaX\entities\loot\functions;

use CLADevs\VanillaX\enchantments\EnchantmentManager;
use CLADevs\VanillaX\entities\loot\LootFunction;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\utils\Random;

class EnchantRandomGearFunction extends LootFunction{

    const NAME = "enchant_random_gear";

    private float $chance;

    public function __construct(float $chance){
        $this->chance = $chance;
    }

    public function apply(Item $item): void{
        $random = new Random();

        if($random->nextFloat() <= $this->chance){
            $enchantments = EnchantmentManager::getEnchantmentForItem($item);

            if($enchantments !== null){
                $enchant = Enchantment::getEnchantment($enchantments[array_rand($enchantments)]);

                if($enchant !== null){
                    $item->addEnchantment(new EnchantmentInstance($enchant, mt_rand(1, $enchant->getMaxLevel())));
                }
            }
        }
    }
}