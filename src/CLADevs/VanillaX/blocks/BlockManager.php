<?php

namespace CLADevs\VanillaX\blocks;

use CLADevs\VanillaX\blocks\block\CommandBlock;
use CLADevs\VanillaX\blocks\block\redstone\RedstoneComparator;
use CLADevs\VanillaX\blocks\block\redstone\RedstoneLamp;
use CLADevs\VanillaX\blocks\block\redstone\RedstoneRepeater;
use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use CLADevs\VanillaX\items\ItemManager;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use CLADevs\VanillaX\utils\Utils;
use CLADevs\VanillaX\VanillaX;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\FloorSign;
use pocketmine\block\tile\Spawnable;
use pocketmine\block\tile\TileFactory;
use pocketmine\block\WallSign;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ToolTier;
use pocketmine\network\mcpe\convert\RuntimeBlockMapping;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use const pocketmine\RESOURCE_PATH;

class BlockManager{
    use SingletonTrait;

    public function __construct(){
        self::setInstance($this);
    }

    /**
     * @throws ReflectionException
     */
    public function startup(): void{
        $this->initializeRuntimeIds();
        $this->initializeBlocks();
        $this->initializeTiles();

        Server::getInstance()->getAsyncPool()->addWorkerStartHook(function(int $worker): void{
            Server::getInstance()->getAsyncPool()->submitTaskToWorker(new class() extends AsyncTask{

                public function onRun(): void{
                    BlockManager::getInstance()->initializeRuntimeIds();
                }
            }, $worker);
        });
    }

    public function initializeRuntimeIds(): void{
        $instance = RuntimeBlockMapping::getInstance();
        $method = new ReflectionMethod(RuntimeBlockMapping::class, "registerMapping");
        $method->setAccessible(true);

        $blockIdMap = json_decode(file_get_contents(RESOURCE_PATH . 'vanilla/block_id_map.json'), true);
        $metaMap = [];
        foreach($instance->getBedrockKnownStates() as $runtimeId => $nbt){
            $mcpeName = $nbt->getString("name");
            $meta = isset($metaMap[$mcpeName]) ? ($metaMap[$mcpeName] + 1) : 0;
            $id = $blockIdMap[$mcpeName] ?? BlockLegacyIds::AIR;

            if($id !== BlockLegacyIds::AIR && !BlockFactory::getInstance()->isRegistered($id, $meta)){
                //var_dump("Runtime: $runtimeId Id: $id Name: $mcpeName Meta $meta");
                $metaMap[$mcpeName] = $meta;
                $method->invoke($instance, $runtimeId, $id, $meta);
            }
        }
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
        $this->registerRedstone();
        $this->registerCommandBlock();

        self::registerBlock(new Block(new BlockIdentifier(BlockLegacyIds::SLIME_BLOCK, 0), "Slime", new BlockBreakInfo(0)));
        self::registerBlock(new Block(new BlockIdentifier(BlockVanilla::ANCIENT_DEBRIS, 0, ItemIdentifiers::ANCIENT_DEBRIS), "Ancient Debris", new BlockBreakInfo(5.0, BlockToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel(), 6000.0)));
    }

    private function registerRedstone(): void{
        self::registerBlock(new RedstoneComparator(true));
        self::registerBlock(new RedstoneComparator(false));
        self::registerBlock(new RedstoneRepeater(true));
        self::registerBlock(new RedstoneRepeater(false));
        self::registerBlock(new RedstoneLamp());
    }

    private function registerCommandBlock(): void{
        self::registerBlock(new CommandBlock(BlockLegacyIds::COMMAND_BLOCK));
        self::registerBlock(new CommandBlock(BlockLegacyIds::REPEATING_COMMAND_BLOCK));
        self::registerBlock(new CommandBlock(BlockLegacyIds::CHAIN_COMMAND_BLOCK));
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

    public function registerBlock(Block $block, bool $override = true, bool $creativeItem = false): bool{
        if(in_array($block->getId(), VanillaX::getInstance()->getConfig()->getNested("disabled.blocks", []))){
            return false;
        }
        BlockFactory::getInstance()->register($block, $override);
        $item = $block->asItem();
        $itemBlock = $item->getBlock();

        if($itemBlock->getId() !== $block->getId() || $itemBlock->getMeta() !== $block->getMeta()){
            ItemManager::register(new class(new ItemIdentifier($item->getId(), $item->getMeta()), $block->getName(), $block) extends Item{

                private Block $block;
                public function __construct(ItemIdentifier $identifier, string $name, Block $block){
                    parent::__construct($identifier, $name);
                    $this->block = $block;
                }

                public function getBlock(?int $clickedFace = null): Block{
                    return $this->block;
                }
            }, $creativeItem, true);
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