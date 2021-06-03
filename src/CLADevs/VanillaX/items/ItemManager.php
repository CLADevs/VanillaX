<?php

namespace CLADevs\VanillaX\items;

use CLADevs\VanillaX\enchantments\EnchantmentManager;
use CLADevs\VanillaX\items\types\HorseArmorItem;
use CLADevs\VanillaX\items\types\MapItem;
use CLADevs\VanillaX\items\types\MinecartItem;
use CLADevs\VanillaX\items\types\MusicDiscItem;
use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\items\utils\NonCreativeItemTrait;
use CLADevs\VanillaX\items\utils\NonOverwriteItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\item\Armor;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

class ItemManager{

    public function startup(): void{
        Utils::callDirectory("items" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void{
            if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                $class = new $namespace();
                self::register($class, !$class instanceof NonCreativeItemTrait, !$class instanceof NonOverwriteItemTrait);
            }
        });

        self::register(new Item(ItemIds::CARROT_ON_A_STICK), true); //ITEM
        self::register(new Item(ItemIds::ENCHANTED_BOOK)); //ITEM
        self::register(new Item(ItemIdentifiers::NETHERITE_INGOT)); //ITEM
        self::register(new Item(ItemIdentifiers::NETHERITE_SCRAP)); //ITEM
        self::register(new Item(ItemIdentifiers::HONEYCOMB, 0, "Honeycomb")); //ITEM
        self::register(new Item(Item::KELP, 0, "Kelp")); //ITEM
        self::register(new Item(Item::DRIED_KELP, 0, "Dried Kelp")); //ITEM
        self::register(new Item(Item::NETHER_WART, 0, "Nether Wart")); //ITEM
        self::register(new Item(Item::TOTEM, 0, "Totem of Undying")); //ITEM
        self::register(new Item(Item::TURTLE_SHELL_PIECE, 0, "Turtle Shell")); //ITEM
        self::register(new Item(Item::PHANTOM_MEMBRANE, 0, "Phantom Membrane")); //ITEM
        self::register(new Item(Item::FIREWORKS_CHARGE, 0, "Fireworks Charge")); //ITEM
        self::register(new MinecartItem(ItemIds::MINECART));
        self::register(new MinecartItem(ItemIds::MINECART_WITH_CHEST, 0, "Chest"));
        self::register(new MinecartItem(ItemIds::MINECART_WITH_TNT, 0, "TNT"));
        self::register(new MinecartItem(ItemIds::MINECART_WITH_HOPPER, 0, "Hopper"));
        self::register(new MinecartItem(ItemIds::MINECART_WITH_COMMAND_BLOCK, 0, "Command Block"));
        self::register(new MapItem(MapItem::FILLED_MAP));
        self::register(new MapItem(MapItem::EMPTY_MAP), true);

        for($i = 416; $i <= 419; $i++){
            self::register(new HorseArmorItem($i), true);
        }

        $musicDics = [
            "13" => LevelSoundEventPacket::SOUND_RECORD_13,
            "cat" => LevelSoundEventPacket::SOUND_RECORD_CAT,
            "blocks" => LevelSoundEventPacket::SOUND_RECORD_BLOCKS,
            "chrip" => LevelSoundEventPacket::SOUND_RECORD_CHIRP,
            "far" => LevelSoundEventPacket::SOUND_RECORD_FAR,
            "mall" => LevelSoundEventPacket::SOUND_RECORD_MALL,
            "mellohi" => LevelSoundEventPacket::SOUND_RECORD_MELLOHI,
            "stal" => LevelSoundEventPacket::SOUND_RECORD_STAL,
            "strad" => LevelSoundEventPacket::SOUND_RECORD_STRAD,
            "ward" => LevelSoundEventPacket::SOUND_RECORD_WARD,
            "11" => LevelSoundEventPacket::SOUND_RECORD_11,
            "wait" => LevelSoundEventPacket::SOUND_RECORD_WAIT,
            "Pigstep" => LevelSoundEventPacket::SOUND_RECORD_PIGSTEP
        ];
        $startId = 500;
        foreach($musicDics as $name => $soundId){
            if($startId === 512){
                self::register(new MusicDiscItem(759, 0, "Lena Raine - " . $name, $soundId), true);
            }else{
                self::register(new MusicDiscItem($startId, 0, "C418 - " . $name, $soundId), true);
            }
            $startId++;
        }

        foreach(EnchantmentManager::getAllEnchantments() as $enchantment){
            $enchant = Enchantment::getEnchantment($enchantment);

            if($enchant instanceof Enchantment){
                for($i = 1; $i <= $enchant->getMaxLevel(); $i++){
                    $item = ItemFactory::get(ItemIds::ENCHANTED_BOOK);
                    $item->addEnchantment(new EnchantmentInstance($enchant, $i));
                    if(!Item::isCreativeItem($item)) Item::addCreativeItem($item);
                }
            }
        }
        for($i = 1; $i <= 15; $i++){
            $item = ItemFactory::get(ItemIds::SHULKER_BOX, $i);
            if(!Item::isCreativeItem($item)) Item::addCreativeItem($item);
        }
    }
    
    public static function register(Item $item, bool $creative = false, bool $overwrite = true): bool{
        if(in_array($item->getId(), VanillaX::getInstance()->getConfig()->getNested("disabled.items", []))){
            return false;
        }
        ItemFactory::registerItem($item, $overwrite);
        if($creative && !Item::isCreativeItem($item)) Item::addCreativeItem($item);
        return true;
    }

    public static function getArmorSlot(Item $item, bool $includeElytra = false): ?int{
        if($item instanceof Armor){
            if(in_array($item->getId(), self::getHelmetList())) return 0;
            if(in_array($item->getId(), self::getChestplateList($includeElytra))) return 1;
            if(in_array($item->getId(), self::getLeggingsList())) return 2;
            if(in_array($item->getId(), self::getBootsList())) return 3;
        }
        return null;
    }

    public static function getHelmetList(): array{
        return [ItemIds::TURTLE_HELMET, ItemIds::LEATHER_HELMET, ItemIds::CHAIN_HELMET, ItemIds::IRON_HELMET, ItemIds::GOLD_HELMET, ItemIds::DIAMOND_HELMET];
    }

    public static function getChestplateList(bool $elytra = false): array{
        $items = [ItemIds::LEATHER_CHESTPLATE, ItemIds::CHAIN_CHESTPLATE, ItemIds::IRON_CHESTPLATE, ItemIds::GOLD_CHESTPLATE, ItemIds::DIAMOND_CHESTPLATE];
        if($elytra){
            $items[] = ItemIds::ELYTRA;
        }
        return $items;
    }

    public static function getLeggingsList(): array{
        return [ItemIds::LEATHER_LEGGINGS, ItemIds::CHAIN_LEGGINGS, ItemIds::IRON_LEGGINGS, ItemIds::GOLD_LEGGINGS, ItemIds::DIAMOND_LEGGINGS];
    }

    public static function getBootsList(): array{
        return [ItemIds::LEATHER_BOOTS, ItemIds::CHAIN_BOOTS, ItemIds::IRON_BOOTS, ItemIds::GOLD_BOOTS, ItemIds::DIAMOND_BOOTS];
    }
}