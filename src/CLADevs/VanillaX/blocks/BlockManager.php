<?php

namespace CLADevs\VanillaX\blocks;

use CLADevs\VanillaX\blocks\tiles\BeaconTile;
use CLADevs\VanillaX\blocks\tiles\CommandBlockTile;
use CLADevs\VanillaX\blocks\tiles\DispenserTile;
use CLADevs\VanillaX\blocks\tiles\DropperTile;
use CLADevs\VanillaX\blocks\tiles\HopperTile;
use CLADevs\VanillaX\blocks\tiles\JukeboxTile;
use CLADevs\VanillaX\blocks\tiles\MobSpawnerTile;
use CLADevs\VanillaX\blocks\types\CommandBlock;
use CLADevs\VanillaX\blocks\types\redstone\RedstoneComparator;
use CLADevs\VanillaX\blocks\types\redstone\RedstoneLamp;
use CLADevs\VanillaX\blocks\types\redstone\RedstoneRepeater;
use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\Utils;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\Server;
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
                BlockFactory::registerBlock(($class = new $namespace()), true);
                if($class instanceof Block && $class->ticksRandomly()){
                    foreach(Server::getInstance()->getLevels() as $level){
                        $level->addRandomTickedBlock($class->getId());
                    }
                }
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
        Tile::registerTile(JukeboxTile::class, [TileIdentifiers::JUKEBOX, "minecraft:jukebox"]);
        Tile::registerTile(BeaconTile::class, [TileIdentifiers::BEACON, "minecraft:beacon"]);
        Tile::registerTile(DispenserTile::class, [TileIdentifiers::DISPENSER, "minecraft:dispenser"]);
        Tile::registerTile(DropperTile::class, [TileIdentifiers::DROPPER, "minecraft:dropper"]);
        //TODO Tile::registerTile(StoneCutterTile::class, [TileIdentifiers::STONECUTTER, "minecraft:stonecutter"]);
    }
}