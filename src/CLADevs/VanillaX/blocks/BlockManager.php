<?php

namespace CLADevs\VanillaX\blocks;

use CLADevs\VanillaX\blocks\tiles\CommandBlockTile;
use CLADevs\VanillaX\blocks\tiles\MobSpawnerTile;
use CLADevs\VanillaX\blocks\types\CommandBlock;
use CLADevs\VanillaX\blocks\types\MobSpawnerBlock;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\tile\Tile;
use ReflectionException;

class BlockManager{

    /**
     * @throws ReflectionException
     */
    public function startup(): void{
        $this->initializeBlocks();
        $this->initializeTiles();
    }

    public function initializeBlocks(): void{
        BlockFactory::registerBlock(new MobSpawnerBlock(), true);

        $blocks = [
            new CommandBlock(BlockIds::COMMAND_BLOCK),
            new CommandBlock(BlockIds::REPEATING_COMMAND_BLOCK),
            new CommandBlock(BlockIds::CHAIN_COMMAND_BLOCK)
        ];
        foreach($blocks as $block){
            BlockFactory::registerBlock($block, true);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function initializeTiles(): void{
        Tile::registerTile(MobSpawnerTile::class, ["MobSpawner", "minecraft:mob_spawner"]);
        Tile::registerTile(CommandBlockTile::class, ["MobSpawner", "minecraft:mob_spawner"]);
    }
}