<?php

namespace CLADevs\VanillaX\blocks;

use CLADevs\VanillaX\blocks\block\CommandBlock;
use CLADevs\VanillaX\blocks\block\redstone\RedstoneComparator;
use CLADevs\VanillaX\blocks\block\redstone\RedstoneLamp;
use CLADevs\VanillaX\blocks\block\redstone\RedstoneRepeater;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\tile\Spawnable;
use pocketmine\block\tile\TileFactory;
use pocketmine\inventory\CreativeInventory;
use pocketmine\Server;
use ReflectionClass;
use ReflectionException;

class BlockManager{

    /**
     * @throws ReflectionException
     */
    public function startup(): void{
        $this->initializeBlocks();
        $this->initializeTiles();
    }

    private function initializeBlocks(): void{
        Utils::callDirectory("blocks" . DIRECTORY_SEPARATOR . "block", function (string $namespace): void{
            if(!isset(class_implements($namespace)[NonAutomaticCallItemTrait::class])){
                if(self::registerBlock(($class = new $namespace()), true, !$class instanceof NonCreativeItemTrait) && $class instanceof Block && $class->ticksRandomly()){
                    foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
                        $world->addRandomTickedBlock($class);
                    }
                }
            }
        });

        self::registerBlock(new RedstoneComparator(true), true);
        self::registerBlock(new RedstoneComparator(false), true);
        self::registerBlock(new RedstoneRepeater(true), true);
        self::registerBlock(new RedstoneRepeater(false), true);
        self::registerBlock(new RedstoneLamp(), true);

        foreach([BlockLegacyIds::COMMAND_BLOCK, BlockLegacyIds::REPEATING_COMMAND_BLOCK, BlockLegacyIds::CHAIN_COMMAND_BLOCK] as $block){
            self::registerBlock(new CommandBlock($block), true);
        }
        self::registerBlock(new Block(new BlockIdentifier(BlockLegacyIds::SLIME_BLOCK, 0), "Slime", new BlockBreakInfo(0)));
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
                    self::registerTile($namespace, $saveNames, $tileBlock === false ? BlockLegacyIds::AIR : $tileBlock);
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
        BlockFactory::getInstance()->register($block, $override);
        if($creativeItem && !CreativeInventory::getInstance()->contains($item = $block->asItem())){
            CreativeInventory::getInstance()->add($item);
        }
        return true;
    }

    /**
     * @param string $namespace Class of the Tile
     * @param array $names Save names for Tile, for such use as Tile::createTile
     * @param array|int $blockId Block the Tile was made for not necessary
     * @return bool returns true if it succeed, if not it returns false
     */
    public function registerTile(string $namespace, array $names = [], $blockId = BlockLegacyIds::AIR): bool{
        if(!is_array($blockId)){
            $blockId = [$blockId];
        }
        foreach($blockId as $id){
            if($id !== BlockLegacyIds::AIR && in_array($id, VanillaX::getInstance()->getConfig()->getNested("disabled.blocks", []))){
                return false;
            }
        }
        TileFactory::getInstance()->register($namespace, $names);
        return true;
    }

    public static function onChange(Spawnable $tile): void{
        foreach($tile->getPos()->getWorld()->createBlockUpdatePackets([$tile->getPos()]) as $pk){
            $tile->getPos()->getWorld()->broadcastPacketToViewers($tile->getPos(), $pk);
        }
    }
}