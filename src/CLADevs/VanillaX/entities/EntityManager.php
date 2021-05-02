<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\loot\LootManager;
use CLADevs\VanillaX\entities\utils\trade\VillagerProfession;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\Entity;

class EntityManager{

    /** @var VillagerProfession[] */
    private array $villagerProfessionList = [];

    private LootManager $lootManager;

    public function __construct(){
        $this->lootManager = new LootManager();
    }

    public function startup(): void{
        if(VanillaX::getInstance()->getConfig()->get("mobs", true)){
            $this->initializeVillagerProfession();
            $this->lootManager->startup();

            $disabledMobs = VanillaX::getInstance()->getConfig()->getNested("disabled.mobs", []);
            foreach(["object", "boss", "passive", "neutral", "monster", "projectile"] as $path){
                Utils::callDirectory("entities" . DIRECTORY_SEPARATOR . $path, function (string $namespace)use($disabledMobs): void{
                    if(!in_array($namespace::NETWORK_ID, $disabledMobs)){
                        Entity::registerEntity($namespace, true);
                    }
                });
            }
        }
    }

    public function initializeVillagerProfession(): void{
        /** This will be moved later on for optimization */
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::UNEMPLOYED, "Villager"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::NITWIT, "Nitwit"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::ARMORER, "Armorer"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::BUTCHER, "Butcher"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::CARTOGRAPHER, "Cartographer"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::CLERIC, "Cleric"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::FARMER, "Farmer"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::FISHERMAN, "Fisherman"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::FLETCHER, "Fletcher"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::LEATHERWORKER, "Leatherworker"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::LIBRARIAN, "Librarian"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::STONE_MASON, "Stone Mason"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::SHEPHERD, "Shepherd"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::TOOLSMITH, "Toolsmith"));
        $this->addVillagerProfession(new VillagerProfession(VillagerProfession::WEAPONSMITH, "Weaponsmith"));
    }

    public function getLootManager(): LootManager{
        return $this->lootManager;
    }

    public function addVillagerProfession(VillagerProfession $profession): void{
        $this->villagerProfessionList[$profession->getId()] = $profession;
    }

    public function getVillagerProfessionFor(int $id): ?VillagerProfession{
        return $this->villagerProfessionList[$id] ?? null;
    }
}