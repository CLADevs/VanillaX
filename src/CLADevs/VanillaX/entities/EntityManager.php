<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\object\PaintingEntity;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Entity;

class EntityManager{

    public function startup(): void{
        VillagerProfession::init();
        if(VanillaX::getInstance()->getConfig()->get("mobs", true)){
            foreach(["object", "boss", "passive", "neutral", "monster", "projectile"] as $path){
                Utils::callDirectory("entities" . DIRECTORY_SEPARATOR . $path, function (string $namespace): void{
                    if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                        self::registerEntity($namespace);
                    }
                });
            }
            self::registerEntity(PaintingEntity::class, true, ['Painting', 'minecraft:painting']);
        }
    }

    public static function registerEntity(string $namespace, bool $force = true, array $saveNames = []): void{
        $disabledMobs = VanillaX::getInstance()->getConfig()->getNested("disabled.mobs", []);

        if(in_array($namespace::NETWORK_ID, $disabledMobs)){
           return;
        }
        Entity::registerEntity($namespace, $force, $saveNames);
    }
}