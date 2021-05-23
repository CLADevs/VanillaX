<?php

namespace CLADevs\VanillaX\blocks;

use CLADevs\VanillaX\blocks\tiles\BeaconTile;
use CLADevs\VanillaX\blocks\tiles\BrewingStandTile;
use CLADevs\VanillaX\blocks\tiles\CommandBlockTile;
use CLADevs\VanillaX\blocks\tiles\DispenserTile;
use CLADevs\VanillaX\blocks\tiles\DropperTile;
use CLADevs\VanillaX\blocks\tiles\HopperTile;
use CLADevs\VanillaX\blocks\tiles\JukeboxTile;
use CLADevs\VanillaX\blocks\tiles\MobSpawnerTile;
use CLADevs\VanillaX\blocks\tiles\ShulkerBoxTile;
use CLADevs\VanillaX\blocks\types\CommandBlock;
use CLADevs\VanillaX\blocks\types\redstone\RedstoneComparator;
use CLADevs\VanillaX\blocks\types\redstone\RedstoneLamp;
use CLADevs\VanillaX\blocks\types\redstone\RedstoneRepeater;
use CLADevs\VanillaX\blocks\types\ShulkerBoxBlock;
use CLADevs\VanillaX\items\utils\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\items\utils\NonCreativeItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
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

    /**
     * @throws ReflectionException
     */
    public function initializeBlocks(): void{
        Utils::callDirectory("blocks" . DIRECTORY_SEPARATOR . "types", function (string $namespace): void{
            if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                if(self::registerBlock(($class = new $namespace()), true, !$class instanceof NonCreativeItemTrait) && $class instanceof Block && $class->ticksRandomly()){
                    foreach(Server::getInstance()->getLevels() as $level){
                        $level->addRandomTickedBlock($class->getId());
                    }
                }
            }
        });

        self::registerBlock(new RedstoneComparator(true), true);
        self::registerBlock(new RedstoneComparator(false), true);
        self::registerBlock(new RedstoneRepeater(true), true);
        self::registerBlock(new RedstoneRepeater(false), true);
        self::registerBlock(new RedstoneLamp(true), true);
        self::registerBlock(new RedstoneLamp(false), true);

        foreach([BlockIds::COMMAND_BLOCK, BlockIds::REPEATING_COMMAND_BLOCK, BlockIds::CHAIN_COMMAND_BLOCK] as $block){
            self::registerBlock(new CommandBlock($block), true);
        }
        self::registerBlock(new ShulkerBoxBlock(BlockIds::SHULKER_BOX));
        self::registerBlock(new ShulkerBoxBlock(BlockIds::UNDYED_SHULKER_BOX, 0, "Shulker Box"));
    }

    /**
     * @throws ReflectionException
     */
    public function initializeTiles(): void{
        self::registerTile(MobSpawnerTile::class, [Tile::MOB_SPAWNER, "minecraft:mob_spawner"], BlockIds::MOB_SPAWNER);
        self::registerTile(CommandBlockTile::class, [TileIdentifiers::COMMAND_BLOCK, "minecraft:command_block"], [BlockIds::COMMAND_BLOCK, BlockIds::CHAIN_COMMAND_BLOCK, BlockIds::REPEATING_COMMAND_BLOCK]);
        self::registerTile(HopperTile::class, [TileIdentifiers::HOPPER, "minecraft:hopper"], BlockIds::HOPPER_BLOCK);
        self::registerTile(JukeboxTile::class, [TileIdentifiers::JUKEBOX, "minecraft:jukebox"], BlockIds::JUKEBOX);
        self::registerTile(BeaconTile::class, [TileIdentifiers::BEACON, "minecraft:beacon"], BlockIds::BEACON);
        self::registerTile(DispenserTile::class, [TileIdentifiers::DISPENSER, "minecraft:dispenser"], BlockIds::DISPENSER);
        self::registerTile(DropperTile::class, [TileIdentifiers::DROPPER, "minecraft:dropper"], BlockIds::DROPPER);
        self::registerTile(BrewingStandTile::class, [Tile::BREWING_STAND, "minecraft:brewing_stand"], BlockIds::BREWING_STAND_BLOCK);
        self::registerTile(ShulkerBoxTile::class, [TileIdentifiers::SHULKER_BOX, "minecraft:shulker_box"], [BlockIds::SHULKER_BOX, BlockIds::UNDYED_SHULKER_BOX]);
        //TODO Tile::registerTile(StoneCutterTile::class, [TileIdentifiers::STONECUTTER, "minecraft:stonecutter"]);
    }

    public function registerBlock(Block $block, bool $override = false, bool $creativeItem = false): bool{
        if(in_array($block->getId(), VanillaX::getInstance()->getConfig()->getNested("disabled.blocks", []))){
            return false;
        }
        BlockFactory::registerBlock($block, $override);
        if($creativeItem && !Item::isCreativeItem($item = ItemFactory::get($block->getItemId()))){
            Item::addCreativeItem($item);
        }
        return true;
    }

    /**
     * @param string $namespace Class of the Tile
     * @param array $names Save names for Tile, for such use as Tile::createTile
     * @param array|int $blockId Block the Tile was made for not necessary
     * @return bool returns true if it succeed, if not it returns false
     * @throws ReflectionException
     */
    public function registerTile(string $namespace, array $names = [], $blockId = BlockIds::AIR): bool{
        if(!is_array($blockId)){
            $blockId = [$blockId];
        }
        foreach($blockId as $id){
            if($id !== BlockIds::AIR && in_array($id, VanillaX::getInstance()->getConfig()->getNested("disabled.blocks", []))){
                return false;
            }
        }
        Tile::registerTile($namespace, $names);
        return true;
    }
}