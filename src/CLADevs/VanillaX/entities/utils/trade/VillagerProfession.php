<?php

namespace CLADevs\VanillaX\entities\utils\trade;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;

class VillagerProfession{

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
}