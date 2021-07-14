<?php

namespace CLADevs\VanillaX\items;

use CLADevs\VanillaX\entities\EntityManager;
use CLADevs\VanillaX\entities\monster\PiglinBruteEntity;
use CLADevs\VanillaX\entities\neutral\GoatEntity;
use CLADevs\VanillaX\entities\passive\AxolotlEntity;
use CLADevs\VanillaX\entities\passive\GlowSquidEntity;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\items\types\HorseArmorItem;
use CLADevs\VanillaX\items\types\MapItem;
use CLADevs\VanillaX\items\types\MinecartItem;
use CLADevs\VanillaX\items\types\MusicDiscItem;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use CLADevs\VanillaX\utils\item\NonOverwriteItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\data\bedrock\EntityLegacyIds;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\SpawnEgg;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\World;
use ReflectionClass;
use const pocketmine\RESOURCE_PATH;

class ItemManager{

    public function startup(): void{
        $this->initializeOverwrite();
        Utils::callDirectory("items" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void{
            if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                $class = new $namespace();
                self::register($class, !$class instanceof NonCreativeItemTrait, !$class instanceof NonOverwriteItemTrait);
            }
        });

        self::register(new Item(new ItemIdentifier(ItemIdentifiers::NETHERITE_INGOT, 0), "Netherite Scrap")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIdentifiers::HONEYCOMB, 0), "Honeycomb")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIdentifiers::BELL, 0), "Bell")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIdentifiers::LANTERN, 0), "Lantern")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIdentifiers::CAMPFIRE, 0), "Campfire")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIdentifiers::DRIED_KELP_BLOCK, 0), "Dried Kelp")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIdentifiers::KELP, 0), "Kelp")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::DRIED_KELP, 0), "Dried Kelp")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::NETHER_WART, 0), "Nether Wart")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::TURTLE_SHELL_PIECE, 0), "Turtle Shell")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::PHANTOM_MEMBRANE, 0), "Phantom Membrane")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::FIREWORKS_CHARGE, 0), "Fireworks Charge")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::ENCHANTED_BOOK, 0), "Enchanted Book")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::CARROT_ON_A_STICK, 0), "Carrot on a Stick")); //ITEM
        self::register(new MinecartItem(ItemIds::MINECART));
        self::register(new MinecartItem(ItemIds::MINECART_WITH_CHEST, "Chest"));
        self::register(new MinecartItem(ItemIds::MINECART_WITH_TNT, "TNT"));
        self::register(new MinecartItem(ItemIds::MINECART_WITH_HOPPER, "Hopper"));
        self::register(new MinecartItem(ItemIds::MINECART_WITH_COMMAND_BLOCK, "Command Block"));
        self::register(new MapItem(MapItem::MAP_FILLED));
        self::register(new MapItem(MapItem::MAP_EMPTY), true);
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
                self::register(new MusicDiscItem(759, "Lena Raine - " . $name, $soundId));
            }else{
                self::register(new MusicDiscItem($startId, "C418 - " . $name, $soundId));
            }
            $startId++;
        }

        /** Spawn Egg */
//        $entityIds = (new ReflectionClass(EntityIds::class))->getConstants();
//        $entityLegacyIds = (new ReflectionClass(EntityLegacyIds::class))->getConstants();
//
//        foreach(EntityManager::getInstance()->getEntities() as $namespace){
//            $networkId = $namespace::NETWORK_ID;
//            $key = array_search($networkId, $entityIds);
//            $id = $entityLegacyIds[$key] ?? null;
//
//            if($id === null){
//                switch($networkId){
//                    case GlowSquidEntity::NETWORK_ID:
//                        $key = "GLOW_SQUID";
//                        $id = VanillaEntity::GLOW_SQUID;
//                        break;
//                    case GoatEntity::NETWORK_ID:
//                        $key = "GOAT";
//                        $id = VanillaEntity::GOAT;
//                        break;
//                    case AxolotlEntity::NETWORK_ID:
//                        $key = "AXOLOTL";
//                        $id = VanillaEntity::AXOLOTL;
//                        break;
//                    case PiglinBruteEntity::NETWORK_ID:
//                        $key = "PIGLIN_BRUTE";
//                        $id = VanillaEntity::PIGLIN_BRUTE;
//                        break;
//                }
//            }
//
//            if($id !== null){
//                $entityName = [];
//                foreach(explode("_", $key) as $value){
//                    $entityName[] = ucfirst(strtolower($value));
//                }
//                $entityName = implode(" ", $entityName);
//                ItemFactory::getInstance()->register(new class(new ItemIdentifier(ItemIds::SPAWN_EGG, $id), $entityName . " Spawn Egg", $namespace) extends SpawnEgg{
//                    private string $namespace;
//
//                    public function __construct(ItemIdentifier $identifier, string $name = "Unknown", string $namespace = ""){
//                        parent::__construct($identifier, $name);
//                        $this->namespace = $namespace;
//                    }
//
//                    public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch): Entity{
//                        return new $this->namespace(Location::fromObject($pos, $world, $yaw, $pitch));
//                    }
//                }, true);
//            }
//        }
        $this->initializeCreativeItems();
    }

    private function initializeCreativeItems(): void{
        $oldCreativeItems = CreativeInventory::getInstance()->getAll();
        CreativeInventory::getInstance()->clear();
        $creativeItems = json_decode(file_get_contents(RESOURCE_PATH . "vanilla" . DIRECTORY_SEPARATOR . "creativeitems.json"), true);

        foreach($creativeItems as $data){
            $item = Item::jsonDeserialize($data);
            if($item->getName() === "Unknown"){
                continue;
            }
            CreativeInventory::getInstance()->add($item);
        }
        foreach($oldCreativeItems as $item){
            if(!CreativeInventory::getInstance()->contains($item)) CreativeInventory::getInstance()->add($item);
        }
    }

    private function initializeOverwrite(): void{
        $this->addComplexItem("minecraft:glow_squid_spawn_egg", "minecraft:spawn_egg", VanillaEntity::GLOW_SQUID);
        $this->addComplexItem("minecraft:axolotl_spawn_egg", "minecraft:spawn_egg", VanillaEntity::AXOLOTL);
    }

    private function addComplexItem(string $newId, string $oldId, int $meta): void{
//        $runtimeBlockLegacyIds = json_decode(file_get_contents(RESOURCE_PATH . '/vanilla/required_item_list.json'), true);
//        $legacyStringToIntMap = json_decode(file_get_contents(RESOURCE_PATH . '/vanilla/item_id_map.json'), true);
//        $id = $legacyStringToIntMap[$oldId];
//        $netId = $runtimeBlockLegacyIds[$newId]["runtime_id"];
//
//        /** complexCoreToNetMapping */
//        $property = new ReflectionProperty(ItemTranslator::class, "complexCoreToNetMapping");
//        $property->setAccessible(true);
//        $value = $property->getValue(ItemTranslator::getInstance());
//        $value[$id][$meta] = $netId;
//        $property->setValue(ItemTranslator::getInstance(), $value);
//
//        /** complexNetToCoreMapping */
//        $property = new ReflectionProperty(ItemTranslator::class, "complexNetToCoreMapping");
//        $property->setAccessible(true);
//        $value = $property->getValue(ItemTranslator::getInstance());
//        $value[$netId] = [$id, $meta];
//        $property->setValue(ItemTranslator::getInstance(), $value);
    }
    
    public static function register(Item $item, bool $creative = false, bool $overwrite = true): bool{
        if(in_array($item->getId(), VanillaX::getInstance()->getConfig()->getNested("disabled.items", []))){
            return false;
        }
        ItemFactory::getInstance()->register($item, $overwrite);
        if($creative && !CreativeInventory::getInstance()->contains($item)) CreativeInventory::getInstance()->add($item);
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
        return [ItemIds::TURTLE_HELMET, ItemIds::LEATHER_HELMET, ItemIds::CHAIN_HELMET, ItemIds::IRON_HELMET, ItemIds::GOLD_HELMET, ItemIds::DIAMOND_HELMET, ItemIdentifiers::NETHERITE_HELMET];
    }

    public static function getChestplateList(bool $elytra = false): array{
        $items = [ItemIds::LEATHER_CHESTPLATE, ItemIds::CHAIN_CHESTPLATE, ItemIds::IRON_CHESTPLATE, ItemIds::GOLD_CHESTPLATE, ItemIds::DIAMOND_CHESTPLATE, ItemIdentifiers::NETHERITE_CHESTPLATE];
        if($elytra){
            $items[] = ItemIds::ELYTRA;
        }
        return $items;
    }

    public static function getLeggingsList(): array{
        return [ItemIds::LEATHER_LEGGINGS, ItemIds::CHAIN_LEGGINGS, ItemIds::IRON_LEGGINGS, ItemIds::GOLD_LEGGINGS, ItemIds::DIAMOND_LEGGINGS, ItemIdentifiers::NETHERITE_LEGGINGS];
    }

    public static function getBootsList(): array{
        return [ItemIds::LEATHER_BOOTS, ItemIds::CHAIN_BOOTS, ItemIds::IRON_BOOTS, ItemIds::GOLD_BOOTS, ItemIds::DIAMOND_BOOTS, ItemIdentifiers::NETHERITE_BOOTS];
    }
}