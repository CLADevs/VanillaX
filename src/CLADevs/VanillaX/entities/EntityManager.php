<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\object\PaintingEntity;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Entity;
use pocketmine\entity\EntityDataHelper;
use pocketmine\entity\EntityFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\World;

class EntityManager{

    public function startup(): void{
        VillagerProfession::init();
        if(VanillaX::getInstance()->getConfig()->get("mobs", true)){
            foreach(["object", "projectile", "boss", "passive", "neutral", "monster"] as $path){
                Utils::callDirectory("entities" . DIRECTORY_SEPARATOR . $path, function (string $namespace): void{
                    if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                        self::registerEntity($namespace, [$namespace::NETWORK_ID]);
                    }
                });
            }
            //TODO painting
        }
    }

    public static function registerEntity(string $namespace, array $saveNames = []): void{
        $disabledMobs = VanillaX::getInstance()->getConfig()->getNested("disabled.mobs", []);

        if(in_array($namespace::NETWORK_ID, $disabledMobs)){
           return;
        }
        EntityFactory::getInstance()->register($namespace, function(World $world, CompoundTag $nbt)use($namespace): Entity{
            return new $namespace(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, $saveNames);
    }
}