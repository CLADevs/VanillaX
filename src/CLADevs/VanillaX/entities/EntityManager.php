<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\loot\LootManager;
use CLADevs\VanillaX\entities\passive\VillagerEntity;
use CLADevs\VanillaX\entities\utils\trade\VillagerProfession;
use CLADevs\VanillaX\utils\Utils;
use pocketmine\entity\Entity;

class EntityManager{

    /** @var VillagerProfession[] */
    private array $villagerProfessionList = [];

    private LootManager $lootManager;

    public function __construct(){
        $this->lootManager = new LootManager();
    }

    public function startup(): void{
        $this->initializeVillagerProfession();
        $this->lootManager->startup();

        $callable = function (string $namespace): void{
            Entity::registerEntity($namespace, true);
        };
        foreach(["object", "passive", "monster", "projectile"] as $path){
            Utils::callDirectory("entities" . DIRECTORY_SEPARATOR . $path, $callable);
        }
    }

    public function initializeVillagerProfession(): void{
        /** This will be moved later on for optimization */
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::UNEMPLOYED, "Villager"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::NITWIT, "Nitwit"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::ARMORER, "Armorer"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::BUTCHER, "Butcher"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::CARTOGRAPHER, "Cartographer"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::CLERIC, "Cleric"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::FARMER, "Farmer"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::FISHERMAN, "Fisherman"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::FLETCHER, "Fletcher"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::LEATHERWORKER, "Leatherworker"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::LIBRARIAN, "Librarian"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::STONE_MASON, "Stone Mason"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::SHEPHERD, "Shepherd"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::TOOLSMITH, "Toolsmith"));
        $this->addVillagerProfession(new VillagerProfession(VillagerEntity::WEAPONSMITH, "Weaponsmith"));
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

    public function registefrEntity(string $directory): void{
        $path = __DIR__ . DIRECTORY_SEPARATOR . $directory;

        foreach(array_diff(scandir($path), [".", ".."]) as $file){
            if(is_dir($path . DIRECTORY_SEPARATOR . $file)){
                $this->registerEntity($directory . DIRECTORY_SEPARATOR . $file);
            }else{
                $i = explode(".", $file);
                $extension = $i[count($i) - 1];

                if($extension === "php"){
                    $name = $i[0];
                    $namespace = "";
                    $i = explode(DIRECTORY_SEPARATOR, str_replace(getcwd() . DIRECTORY_SEPARATOR, "", __DIR__));
                    for($v = 0; $v <= 2; $v++){
                        unset($i[$v]);
                    }
                    foreach($i as $key => $string){
                        $namespace .= $string . DIRECTORY_SEPARATOR;
                    }
                    $namespace .= $directory . DIRECTORY_SEPARATOR . $name;
                    Entity::registerEntity($namespace, true);
                }
            }
        }
    }
}