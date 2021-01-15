<?php

namespace CLADevs\VanillaX\entities\utils\trade;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;

class VillagerProfession{

    const UNEMPLOYED = 0;
    const NITWIT = 1;
    const ARMORER = 2;
    const BUTCHER = 3;
    const CARTOGRAPHER = 4;
    const CLERIC = 5;
    const FARMER = 6;
    const FISHERMAN = 7;
    const FLETCHER = 8;
    const LEATHERWORKER = 9;
    const LIBRARIAN = 10;
    const STONE_MASON = 11; //MASON IN JAVA
    const SHEPHERD = 12;
    const TOOLSMITH = 13;
    const WEAPONSMITH = 14;

    private int $id;
    private string $name;
    private Block $block;

    /**
     * VillagerProfession constructor.
     * @param int $id
     * @param string $name
     * @param Block|int $block
     */
    public function __construct(int $id, string $name, $block = BlockIds::AIR){
        $this->id = $id;
        $this->name = $name;
        if(is_int($block)){
            $block = BlockFactory::get($block);
        }
        $this->block = $block;
    }

    public function getId(): int{
        return $this->id;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getBlock(): Block{
        return $this->block;
    }

//    abstract public function getNovice(): array;
//    abstract public function getApprentice(): array;
//    abstract public function getJourneyman(): array;
//    abstract public function getExpert(): array;
//    abstract public function getMaster(): array;
}