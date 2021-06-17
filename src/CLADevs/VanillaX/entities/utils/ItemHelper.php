<?php

namespace CLADevs\VanillaX\entities\utils;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\VanillaX;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Random;

class ItemHelper{

    public static function applyEnchantRandomGear(Item $item, int $chance): void{
        $random = new Random();

        if($random->nextFloat() <=$chance){
            $enchantments = VanillaX::getInstance()->getEnchantmentManager()->getEnchantmentForItem($item);

            if($enchantments !== null){
                $enchant = Enchantment::getEnchantment($enchantments[array_rand($enchantments)]);

                if($enchant !== null){
                    $item->addEnchantment(new EnchantmentInstance($enchant, mt_rand(1, $enchant->getMaxLevel())));
                }
            }
        }
    }

    public static function applyEnchantRandomly(Item $item, bool $treasure): void{
        if($treasure){
            $enchantments = [];
            /** @var EnchantmentTrait|Enchantment $enchantment */
            foreach(VanillaX::getInstance()->getEnchantmentManager()->getEnchantments() as $enchantment){
                if($enchantment->isTreasure()){
                    $enchantments[] = $enchantment->getId();
                }
            }
        }else{
            $enchantments = VanillaX::getInstance()->getEnchantmentManager()->getAllEnchantments(false);
        }
        $enchant = Enchantment::getEnchantment($enchantments[array_rand($enchantments)]);
        $item->addEnchantment(new EnchantmentInstance($enchant, mt_rand(1, $enchant->getMaxLevel())));
    }

    public static function applyEnchantWithLevel(Item $item, bool $treasure, int $min, int $max): void{
        if($treasure){
            $enchantments = VanillaX::getInstance()->getEnchantmentManager()->getTreasureEnchantsId();
        }else{
            $enchantments = VanillaX::getInstance()->getEnchantmentManager()->getAllEnchantments(false);
        }
        $level = $min;
        if($max !== 0){
            $level = mt_rand($min, $max);
        }
        $level /= 10;
        $enchant = Enchantment::getEnchantment($enchantments[array_rand($enchantments)]);
        $item->addEnchantment(new EnchantmentInstance($enchant, min(round($level), $enchant->getMaxLevel())));
    }

    public static function applyFurnaceSmelt(Item &$item): void{
        foreach(Server::getInstance()->getCraftingManager()->getFurnaceRecipes() as $furnace){
            if($furnace->getInput()->getId() === $item->getId()){
                $item = $furnace->getResult();
            }
        }
    }

    public static function applyLootingEnchant(VanillaEntity $entity, Item $item): void{
        $lastDamage = $entity->getLastDamageCause();

        if($lastDamage instanceof EntityDamageByEntityEvent){
            $player = $lastDamage->getDamager();

            if($player instanceof Player && ($level = $player->getInventory()->getItemInHand()->getEnchantmentLevel(Enchantment::LOOTING)) > 0){
                $item->setCount($item->getCount() + mt_rand(0, $level));
            }
        }
    }

    public static function applyRandomAuxValue(Item $item, int $min, int $max): void{
        $item->setDamage(mt_rand($min, $max));
    }

    public static function applySetCount(Item $item, int $min, int $max): void{
        $count = $min;
        if($max !== 0){
            $count = mt_rand($min, $max);
        }
        $item->setCount($item->getCount() + $count);
    }

    public static function applySetDamage(Item $item, int $min, int $max): void{
        if($item instanceof Durable){
            $maxDurability = $item->getMaxDurability();
            $chance = mt_rand($min * 10, $max * 10) / 10;
            $item->setDamage(min($chance * 100, $maxDurability));
        }
    }

    public static function applySetData(Item $item, int $data): void{
        $item->setDamage($data);
    }

    public static function applySpecificEnchants(Item $item, array $enchants): void{
        foreach($enchants as $i){
            $enchant = Enchantment::getEnchantment($i["id"]);

            if($enchant !== null){
                $level = mt_rand($i["level"][0], $i["level"][1]);
                $item->addEnchantment(new EnchantmentInstance($enchant, $level));
            }
        }
    }
}