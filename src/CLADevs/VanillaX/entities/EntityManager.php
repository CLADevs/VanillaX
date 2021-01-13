<?php

namespace CLADevs\VanillaX\entities;

use CLADevs\VanillaX\entities\loot\LootManager;
use pocketmine\entity\Entity;

class EntityManager{

    private LootManager $lootManager;

    public function __construct(){
        $this->lootManager = new LootManager();
    }

    public function startup(): void{
        $this->lootManager->startup();
        $this->registerEntity("object");
        $this->registerEntity("passive");
        $this->registerEntity("monster");
        $this->registerEntity("projectile");
    }

    public function getLootManager(): LootManager{
        return $this->lootManager;
    }

    public function registerEntity(string $directory): void{
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