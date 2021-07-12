<?php

namespace CLADevs\VanillaX\entities\utils;

use CLADevs\VanillaX\enchantments\utils\EnchantmentTrait;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\VanillaX;
use pocketmine\color\Color;
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

    /** All dye colors in array rgb */
    const DYE_BLACK = [29, 29, 33];
    const DYE_RED = [176, 40, 38];
    const DYE_GREEN = [94, 124, 22];
    const DYE_BROWN = [131, 84, 50];
    const DYE_BLUE = [60, 68, 170];
    const DYE_PURPLE = [137, 50, 184];
    const DYE_CYAN = [22, 156, 156];
    const DYE_LIGHT_GRAY = [157, 157, 151];
    const DYE_GRAY = [71, 79, 82];
    const DYE_PINK = [243, 139, 174];
    const DYE_LIME = [128, 199, 31];
    const DYE_YELLOW = [254, 215, 61];
    const DYE_MAGENTA = [199, 78, 189];
    const DYE_ORANGE = [249, 128, 29];
    const DYE_WHITE = [249, 255, 254];

    public static function applyEnchantRandomGear(Item $item, int $chance): void{
        $random = new Random();

        if($random->nextFloat() <= $chance){
            $enchantments = VanillaX::getInstance()->getEnchantmentManager()->getEnchantmentForItem($item);

            if($enchantments !== null){
                $enchant = EnchantmentIdMap::getInstance()->fromId($enchantments[array_rand($enchantments)]);

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
                    $enchantments[] = $enchantment->getRuntimeId();
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
            $enchantments = VanillaX::getInstance()->getEnchantmentManager()->getTreasureEnchantsId();
        }else{
            $enchantments = VanillaX::getInstance()->getEnchantmentManager()->getAllEnchantments(false);
        }
        $level = $min;
        if($max !== 0){
            $level = mt_rand($min, $max);
        }
        $level /= 10;
        $enchant = EnchantmentIdMap::getInstance()->fromId($enchantments[array_rand($enchantments)]);
        $item->addEnchantment(new EnchantmentInstance($enchant, min(round($level), $enchant->getMaxLevel())));
    }

    public static function applyFurnaceSmelt(Item &$item): void{
        foreach(Server::getInstance()->getCraftingManager()->getFurnaceRecipeManager()->getAll() as $furnace){
            if($furnace->getInput()->getId() === $item->getId()){
                $item = $furnace->getResult();
            }
        }
    }

    public static function applyLootingEnchant(VanillaEntity $entity, Item $item): void{
        $lastDamage = $entity->getLastDamageCause();

        if($lastDamage instanceof EntityDamageByEntityEvent){
            $player = $lastDamage->getDamager();

            if($player instanceof Player && ($level = $player->getInventory()->getItemInHand()->getEnchantmentLevel(EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::LOOTING))) > 0){
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

            if($enchant !== null){
                $level = mt_rand($i["level"][0], $i["level"][1]);
                $item->addEnchantment(new EnchantmentInstance($enchant, $level));
            }
        }
    }

    public static function applyRandomDye(Item $item): void{
        if($item instanceof Armor){
            $colors = [
                self::DYE_BLACK, self::DYE_RED, self::DYE_GREEN, self::DYE_BROWN,
                self::DYE_BLUE, self::DYE_PURPLE, self::DYE_CYAN, self::DYE_LIGHT_GRAY,
                self::DYE_GRAY, self::DYE_PINK, self::DYE_LIME, self::DYE_YELLOW,
                self::DYE_MAGENTA, self::DYE_ORANGE, self::DYE_WHITE
            ];
            $item->setCustomColor(new Color(...$colors[array_rand($colors)]));
        }
    }
}