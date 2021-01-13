<?php

namespace CLADevs\VanillaX\enchantments\types;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\item\enchantment\Enchantment;

class BindingEnchantment extends Enchantment{

    public function __construct(){
        parent::__construct(self::BINDING, "Curse of Binding", self::RARITY_RARE, self::SLOT_ARMOR, self::SLOT_ELYTRA, 1);
    }

    public function handle(InventoryTransactionEvent $event): void{
        //Already handled on EnchantmentManager
    }
}