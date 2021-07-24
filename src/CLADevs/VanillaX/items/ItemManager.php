<?php

namespace CLADevs\VanillaX\items;

use CLADevs\VanillaX\entities\EntityManager;
use CLADevs\VanillaX\entities\utils\EntityIdentifierX;
use CLADevs\VanillaX\items\types\HorseArmorItem;
use CLADevs\VanillaX\items\types\MapItem;
use CLADevs\VanillaX\items\types\MinecartItem;
use CLADevs\VanillaX\items\types\MusicDiscItem;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use CLADevs\VanillaX\utils\item\NonOverwriteItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\SpawnEgg;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\world\World;
use const pocketmine\RESOURCE_PATH;

class ItemManager{

    public function startup(): void{
        Utils::callDirectory("items" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void{
            if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                $class = new $namespace();
                self::register($class, !$class instanceof NonCreativeItemTrait, !$class instanceof NonOverwriteItemTrait);
            }
        });

        self::register(new Item(new ItemIdentifier(ItemIdentifiers::NETHERITE_INGOT, 0), "Netherite Scrap")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIdentifiers::HONEYCOMB, 0), "Honeycomb")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::LANTERN, 0), "Lantern")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::CAMPFIRE, 0), "Campfire")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::DRIED_KELP_BLOCK, 0), "Dried Kelp")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::KELP, 0), "Kelp")); //ITEM
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
        foreach(EntityManager::getInstance()->getEntities() as $entity){
            if(in_array($entity->getType(), [EntityIdentifierX::TYPE_MONSTER, EntityIdentifierX::TYPE_NEUTRAL, EntityIdentifierX::TYPE_PASSIVE])){
                ItemFactory::getInstance()->register($item = new class(new ItemIdentifier(ItemIds::SPAWN_EGG, $entity->getId()), $entity->getName() . " Spawn Egg", $entity->getNamespace()) extends SpawnEgg{
                    private string $namespace;

                    public function __construct(ItemIdentifier $identifier, string $name = "Unknown", string $namespace = ""){
                        parent::__construct($identifier, $name);
                        $this->namespace = $namespace;
                    }

                    public function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch): Entity{
                        return new $this->namespace(Location::fromObject($pos, $world, $yaw, $pitch));
                    }
                }, true);
                if(!CreativeInventory::getInstance()->contains($item)){
                    CreativeInventory::getInstance()->add($item);
                }
            }
        }
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
    
    public static function register(Item $item, bool $creative = false, bool $overwrite = true): bool{
        if(in_array($item->getId(), VanillaX::getInstance()->getConfig()->getNested("disabled.items", []))){
            return false;
        }
        ItemFactory::getInstance()->register($item, $overwrite);
        if($creative && !CreativeInventory::getInstance()->contains($item)) CreativeInventory::getInstance()->add($item);
        return true;
    }
}