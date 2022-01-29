<?php

namespace CLADevs\VanillaX\items;

use CLADevs\VanillaX\configuration\features\ItemFeature;
use CLADevs\VanillaX\entities\EntityManager;
use CLADevs\VanillaX\entities\utils\EntityIdentifier;
use CLADevs\VanillaX\items\types\HorseArmorItem;
use CLADevs\VanillaX\items\types\MusicDiscItem;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use CLADevs\VanillaX\utils\item\NonOverwriteItemTrait;
use CLADevs\VanillaX\utils\Utils;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\SpawnEgg;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\Server;
use pocketmine\world\World;
use const pocketmine\BEDROCK_DATA_PATH;

class ItemManager{

    public function startup(): void{
        Utils::callDirectory("items" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void{
            if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                $class = new $namespace();
                self::register($class, !$class instanceof NonCreativeItemTrait, !$class instanceof NonOverwriteItemTrait);
            }
        });

        self::register(new Item(new ItemIdentifier(LegacyItemIds::NETHERITE_INGOT, 0), "Netherite Ingot"), true); //ITEM
        self::register(new Item(new ItemIdentifier(LegacyItemIds::NETHERITE_SCRAP, 0), "Netherite Scrap"), true); //ITEM
        self::register(new Item(new ItemIdentifier(LegacyItemIds::HONEYCOMB, 0), "Honeycomb")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::LANTERN, 0), "Lantern")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::CAMPFIRE, 0), "Campfire")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::DRIED_KELP_BLOCK, 0), "Dried Kelp")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::KELP, 0), "Kelp")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::DRIED_KELP, 0), "Dried Kelp")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::TURTLE_SHELL_PIECE, 0), "Turtle Shell")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::PHANTOM_MEMBRANE, 0), "Phantom Membrane")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::FIREWORKS_CHARGE, 0), "Fireworks Charge")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::CARROT_ON_A_STICK, 0), "Carrot on a Stick")); //ITEM
        for($i = 416; $i <= 419; $i++){
            self::register(new HorseArmorItem($i), true);
        }

        $musicDics = [
            "13" => LevelSoundEvent::RECORD_13,
            "cat" => LevelSoundEvent::RECORD_CAT,
            "blocks" => LevelSoundEvent::RECORD_BLOCKS,
            "chrip" => LevelSoundEvent::RECORD_CHIRP,
            "far" => LevelSoundEvent::RECORD_FAR,
            "mall" => LevelSoundEvent::RECORD_MALL,
            "mellohi" => LevelSoundEvent::RECORD_MELLOHI,
            "stal" => LevelSoundEvent::RECORD_STAL,
            "strad" => LevelSoundEvent::RECORD_STRAD,
            "ward" => LevelSoundEvent::RECORD_WARD,
            "11" => LevelSoundEvent::RECORD_11,
            "wait" => LevelSoundEvent::RECORD_WAIT,
            "Pigstep" => LevelSoundEvent::RECORD_PIGSTEP
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
            if(in_array($entity->getType(), [EntityIdentifier::TYPE_MONSTER, EntityIdentifier::TYPE_NEUTRAL, EntityIdentifier::TYPE_PASSIVE])){
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
        $creativeItems = json_decode(file_get_contents(BEDROCK_DATA_PATH . "creativeitems.json"), true);

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
        if(!ItemFeature::getInstance()->isItemEnabled($item)){
            var_dump(ItemFeature::getInstance()->getItemIdMap()[$item->getId()]);
            return false;
        }
        if(isset(class_uses($item)[RecipeItemTrait::class])){
            /** @var RecipeItemTrait $item */
            $shapeless = $item->getShapelessRecipe();
            $shaped = $item->getShapedRecipe();

            if($shapeless !== null){
                Server::getInstance()->getCraftingManager()->registerShapelessRecipe($shapeless);
            }
            if($shaped !== null){
                Server::getInstance()->getCraftingManager()->registerShapedRecipe($shaped);
            }
        }
        ItemFactory::getInstance()->register($item, $overwrite);
        if($creative && !CreativeInventory::getInstance()->contains($item)) CreativeInventory::getInstance()->add($item);
        return true;
    }
}