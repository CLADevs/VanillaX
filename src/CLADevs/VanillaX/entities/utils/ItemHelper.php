<?php

namespace CLADevs\VanillaX\entities\utils;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\utils\DyeColor;
use pocketmine\crafting\FurnaceType;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Armor;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\Random;

class ItemHelper{

    public static function applyEnchantRandomGear(Item $item, int $chance): void{
        $random = new Random();

        if($random->nextFloat() <= $chance){
            $enchantments = VanillaX::getInstance()->getEnchantmentManager()->getEnchantmentForItem($item);

            if($enchantments !== null){
                $enchant = $enchantments[array_rand($enchantments)];

                if($enchant instanceof Enchantment){
                    $item->addEnchantment(new EnchantmentInstance($enchant, mt_rand(1, $enchant->getMaxLevel())));
                }
            }
        }
    }

    public static function applyEnchantRandomly(Item $item, bool $treasure): void{
        if($treasure){
            $enchantments = [];
            /** @var EnchantmentTrait|Enchantment $enchantment */
            foreach(VanillaX::getInstance()->getEnchantmentManager()->getEnchantmentMap() as $enchantment){
                if($enchantment->isTreasure()){
                    $enchantments[] = $enchantment->getMcpeId();
                }
            }
        }else{
            $enchantments = VanillaX::getInstance()->getEnchantmentManager()->getAllEnchantments(false);
        }
        $enchant = EnchantmentIdMap::getInstance()->fromId($enchantments[array_rand($enchantments)]);
        $item->addEnchantment(new EnchantmentInstance($enchant, mt_rand(1, $enchant->getMaxLevel())));
    }

    public static function applyEnchantWithLevel(Item $item, bool $treasure, int $min, int $max): void{
        if($treasure){
            $enchantments = [];

            /** @var EnchantmentTrait $enchant */
            foreach(VanillaX::getInstance()->getEnchantmentManager()->getEnchantmentMap() as $key => $enchant){
                if($enchant->isTreasure()){
                    $enchantments[$key] = $enchant;
                }
            }
        }else{
            $enchantments = VanillaX::getInstance()->getEnchantmentManager()->getAllEnchantments(false);
        }
        $level = $min;
        if($max !== 0){
            $level = mt_rand($min, $max);
        }
        $level /= 10;
        $enchant = $enchantments[array_rand($enchantments)];

        if($enchant instanceof Enchantment){
            $item->addEnchantment(new EnchantmentInstance($enchant, min(round($level), $enchant->getMaxLevel())));
        }
    }

    public static function applyFurnaceSmelt(Item &$item): void{
        foreach(Server::getInstance()->getCraftingManager()->getFurnaceRecipeManager(FurnaceType::FURNACE())->getAll() as $furnace){
            if($furnace->getInput()->getId() === $item->getId()){
                $item = $furnace->getResult();
            }
        }
    }

    public static function applyLootingEnchant(VanillaEntity $entity, Item $item): void{
        $lastDamage = $entity->getLastDamageCause();

        if($lastDamage instanceof EntityDamageByEntityEvent){
            $player = $lastDamage->getDamager();
            $looting = EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::LOOTING);

            if($player instanceof Player && $looting instanceof Enchantment && ($level = $player->getInventory()->getItemInHand()->getEnchantmentLevel($looting)) > 0){
                $item->setCount($item->getCount() + mt_rand(0, $level));
            }
        }
    }

    public static function applyRandomAuxValue(Item $item, int $min, int $max): void{
        if($item instanceof Durable){
            $item->setDamage(mt_rand($min, $max));
        }
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
        if($item instanceof Durable){
            $item->setDamage($data);
        }
    }

    public static function applySpecificEnchants(Item $item, array $enchants): void{
        foreach($enchants as $i){
            $enchant = EnchantmentIdMap::getInstance()->fromId($i["id"]);

            if($enchant instanceof Enchantment){
                $level = mt_rand($i["level"][0], $i["level"][1]);
                $item->addEnchantment(new EnchantmentInstance($enchant, $level));
            }
        }
    }

    public static function applyRandomDye(Item $item): void{
        if($item instanceof Armor){
            $colors = DyeColor::getAll();
            $item->setCustomColor($colors[array_rand($colors)]->getRgbValue());
        }
    }
}