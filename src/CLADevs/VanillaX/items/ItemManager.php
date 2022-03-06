<?php

namespace CLADevs\VanillaX\items;

use CLADevs\VanillaX\configuration\features\ItemFeature;
use CLADevs\VanillaX\entities\EntityManager;
use CLADevs\VanillaX\items\types\HorseArmorItem;
use CLADevs\VanillaX\items\utils\RecipeItemTrait;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use CLADevs\VanillaX\utils\item\NonOverwriteItemTrait;
use CLADevs\VanillaX\utils\Utils;
use pocketmine\data\bedrock\LegacyItemIdToStringIdMap;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\SpawnEgg;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\World;
use const pocketmine\BEDROCK_DATA_PATH;

class ItemManager{

    public function startup(): void{
        $oldMap = LegacyItemIdToStringIdMap::getInstance()->getStringToLegacyMap();

        foreach(Utils::getItemIdsMap() as $name => $id){
            if(!isset($oldMap[$name])){
                ItemFeature::addLegacy(LegacyItemIdToStringIdMap::getInstance(), $name, $id);
            }
        }
        Utils::callDirectory("items/types", function (string $namespace): void{
            if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                $class = new $namespace();
                self::register($class, !$class instanceof NonCreativeItemTrait, !$class instanceof NonOverwriteItemTrait);
            }
        });

        self::register(new Item(new ItemIdentifier(LegacyItemIds::NETHERITE_INGOT, 0), "Netherite Ingot")); //ITEM
        self::register(new Item(new ItemIdentifier(LegacyItemIds::NETHERITE_SCRAP, 0), "Netherite Scrap")); //ITEM
        self::register(new Item(new ItemIdentifier(LegacyItemIds::HONEYCOMB, 0), "Honeycomb")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::KELP, 0), "Kelp")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::PHANTOM_MEMBRANE, 0), "Phantom Membrane")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::FIREWORKS_CHARGE, 0), "Fireworks Charge")); //ITEM
        self::register(new Item(new ItemIdentifier(ItemIds::CARROT_ON_A_STICK, 0), "Carrot on a Stick")); //ITEM
        for($i = 416; $i <= 419; $i++){
            self::register(new HorseArmorItem($i));
        }

        /** Spawn Egg */
        foreach(EntityManager::getInstance()->getEntityInfoMap() as $key => $info){
            $legacyId = $info->getLegacyId();

            if($key === $legacyId && ($info->isMonster() || $info->isNeutral() || $info->isPassive())){
                $class = new class(new ItemIdentifier(ItemIds::SPAWN_EGG, $legacyId), $info->getDisplayName() . " Spawn Egg") extends SpawnEgg{

                    protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch): Entity{
                        $class = EntityManager::getInstance()->getEntityInfo($this->getMeta())->getClass();
                        return new $class(Location::fromObject($pos, $world, $yaw, $pitch));
                    }
                };
                self::register($class);
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
            return false;
        }
        if(isset(class_uses($item)[RecipeItemTrait::class])){
            /** @var RecipeItemTrait $item */
            $craftingManager = Server::getInstance()->getCraftingManager();
            if(($shapeless = $item->getShapelessRecipe()) !== null) $craftingManager->registerShapelessRecipe($shapeless);
            if(($shaped = $item->getShapedRecipe()) !== null) $craftingManager->registerShapedRecipe($shaped);
        }
        ItemFactory::getInstance()->register($item, $overwrite);

        if($creative){
            $name = ItemFeature::getInstance()->getVanillaName($item);

            if($name !== null && StringToItemParser::getInstance()->parse($name) === null){
                StringToItemParser::getInstance()->register($name, fn() => $item);
            }
            if(!CreativeInventory::getInstance()->contains($item)) CreativeInventory::getInstance()->add($item);
        }
        return true;
    }
}