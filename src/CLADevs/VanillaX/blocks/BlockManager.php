<?php

namespace CLADevs\VanillaX\blocks;

use CLADevs\VanillaX\blocks\block\CommandBlock;
use CLADevs\VanillaX\blocks\block\redstone\RedstoneComparator;
use CLADevs\VanillaX\blocks\block\redstone\RedstoneLamp;
use CLADevs\VanillaX\blocks\block\redstone\RedstoneRepeater;
use CLADevs\VanillaX\blocks\block\ShulkerBoxBlock;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\Server;
use pocketmine\tile\Tile;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

class BlockManager{

    /**
     * @throws ReflectionException
     */
    public function startup(): void{
        $this->initializeOverwrites();
        $this->initializeBlocks();
        $this->initializeTiles();
    }

    private function initializeOverwrites(): void{
        $reflection = new ReflectionProperty(BlockFactory::class, "fullList");
        $reflection->setAccessible(true);
        $value = $reflection->getValue();
        $value->setSize(16384);
        $reflection->setValue(null, $value);

        BlockFactory::$light->setSize(16384);
        BlockFactory::$lightFilter->setSize(16384);
        BlockFactory::$solid->setSize(16384);
        BlockFactory::$hardness->setSize(16384);
        BlockFactory::$transparent->setSize(16384);
        BlockFactory::$diffusesSkyLight->setSize(16384);
        BlockFactory::$blastResistance->setSize(16384);
    }

    private function initializeBlocks(): void{
        Utils::callDirectory("blocks" . DIRECTORY_SEPARATOR . "block", function (string $namespace): void{
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
        self::registerBlock(new ShulkerBoxBlock(BlockIds::SHULKER_BOX), true, true);
        self::registerBlock(new ShulkerBoxBlock(BlockIds::UNDYED_SHULKER_BOX, 0, "Shulker Box"), true, true);
        self::registerBlock(new Block(BlockIds::SLIME_BLOCK, 0, "Slime"));
    }

    /**
     * @throws ReflectionException
     */
    private function initializeTiles(): void{
        $tileConst = [];
        foreach((new ReflectionClass(TileVanilla::class))->getConstants() as $id => $value){
            $tileConst[$value] = $id;
        }

        Utils::callDirectory("blocks" . DIRECTORY_SEPARATOR . "tile", function (string $namespace)use($tileConst): void{
            if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                $rc = new ReflectionClass($namespace);
                $tileID = $rc->getConstant("TILE_ID");
                $tileBlock = $rc->getConstant("TILE_BLOCK");

                if($tileID !== false){
                    $saveNames = [$tileID];
                    $constID = $tileConst[$tileID] ?? null;

                    if($constID !== null){
                        $saveNames[] = "minecraft:" . strtolower($constID);
                    }
                    self::registerTile($namespace, $saveNames, $tileBlock === false ? BlockIds::AIR : $tileBlock);
                }else{
                    VanillaX::getInstance()->getLogger()->error("Tile ID could not be found for '$namespace'");
                }
            }
        });
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