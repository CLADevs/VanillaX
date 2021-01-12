<?php

namespace CLADevs\VanillaX\entities\loot\functions;

use CLADevs\VanillaX\enchantments\EnchantmentManager;
use CLADevs\VanillaX\entities\loot\LootFunction;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;

class EnchantWithLevelsFunction extends LootFunction{

    const NAME = "enchant_with_levels";

    private int $min;
    private int $max;
    private bool $treasure;

    public function __construct(int $min, int $max, bool $treasure){
        $this->min = $min;
        $this->max = $max;
        $this->treasure = $treasure;
        //TODO figure why it uses 30 LOL
    }

    public function apply(Item $item): void{
        if($this->treasure){
            $enchantments = EnchantmentManager::$treasure;
        }else{
            $enchantments = EnchantmentManager::getAllEnchantments(false);
        }
        $level = $this->min;
        if($this->max !== 0){
            $level = mt_rand($this->min, $this->max);
        }
        $level /= 10;
        $enchant = Enchantment::getEnchantment($enchantments[array_rand($enchantments)]);
        $item->addEnchantment(new EnchantmentInstance($enchant, min(round($level), $enchant->getMaxLevel())));
    }
}