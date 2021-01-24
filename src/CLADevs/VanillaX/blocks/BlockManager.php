<?php

namespace CLADevs\VanillaX\blocks;

use CLADevs\VanillaX\blocks\tiles\CommandBlockTile;
use CLADevs\VanillaX\blocks\tiles\HopperTile;
use CLADevs\VanillaX\blocks\tiles\MobSpawnerTile;
use CLADevs\VanillaX\blocks\types\CommandBlock;
use CLADevs\VanillaX\blocks\types\redstone\RedstoneComparator;
use CLADevs\VanillaX\blocks\types\redstone\RedstoneLamp;
use CLADevs\VanillaX\blocks\types\redstone\RedstoneRepeater;
use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\Utils;
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
        Utils::callDirectory("blocks" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void{
            if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                BlockFactory::registerBlock(new $namespace(), true);
            }
        });

        BlockFactory::registerBlock(new RedstoneComparator(true), true);
        BlockFactory::registerBlock(new RedstoneComparator(false), true);
        BlockFactory::registerBlock(new RedstoneRepeater(true), true);
        BlockFactory::registerBlock(new RedstoneRepeater(false), true);
        BlockFactory::registerBlock(new RedstoneLamp(true), true);
        BlockFactory::registerBlock(new RedstoneLamp(false), true);

        foreach([BlockIds::COMMAND_BLOCK, BlockIds::REPEATING_COMMAND_BLOCK, BlockIds::CHAIN_COMMAND_BLOCK] as $block){
            BlockFactory::registerBlock(new CommandBlock($block), true);
        }
    }

    /**
     * @throws ReflectionException
     */
    public function initializeTiles(): void{
        Tile::registerTile(MobSpawnerTile::class, [Tile::MOB_SPAWNER, "minecraft:mob_spawner"]);
        Tile::registerTile(CommandBlockTile::class, [TileIdentifiers::COMMAND_BLOCK, "minecraft:command_block"]);
        Tile::registerTile(HopperTile::class, [TileIdentifiers::HOPPER, "minecraft:hopper"]);
    }
}