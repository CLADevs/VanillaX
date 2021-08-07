<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\items\ItemIdentifiers;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Transparent;
use pocketmine\block\utils\BlockDataSerializer;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\data\bedrock\EnchantmentIds;
use pocketmine\event\block\BlockGrowEvent;
use pocketmine\item\Fertilizer;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class WeepingVinesBlock extends Transparent{

    protected int $age = 0;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockVanilla::WEEPING_VINES, 0, ItemIdentifiers::WEEPING_VINES), "Weeping Vines", BlockBreakInfo::instant());
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($blockReplace->pos->getWorld()->getBlock($blockReplace->pos->add(0, 1, 0)) instanceof Air){
            return false;
        }
        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($item instanceof Fertilizer){
            $item->pop();
            $lowestY = 0;

            for($y = 0; $y <= $this->pos->y; $y++){
                $block = $this->pos->getWorld()->getBlockAt($this->pos->x, $y, $this->pos->z);

                if($block instanceof WeepingVinesBlock){
                    $lowestY = $y;
                    break;
                }
            }
            if(($lowestY - 1) < 0){
                var_dump('Cancelled');
                return true;
            }
            $lowestBlock = $this->pos->getWorld()->getBlockAt($this->pos->x, $lowestY, $this->pos->z);

            if($lowestBlock instanceof WeepingVinesBlock){
                $lastAge = $lowestBlock->getAge();
                $size = mt_rand(1, 6);

                for($i = 1; $i <= $size; $i++){
                    $b = $this->pos->getWorld()->getBlockAt($this->pos->x, $lowestY - $i, $this->pos->z);

                    if($b instanceof Air && ($lowestY - $i) >= 0){
                        if($lastAge > 15){
                            $lastAge = 15;
                        }else{
                            $lastAge++;
                        }
                        $this->pos->getWorld()->setBlock($b->pos, BlockFactory::getInstance()->get(BlockVanilla::WEEPING_VINES, $lastAge > 15 ? 15 : $lastAge));
                    }
                }
            }
            return true;
        }
        return false;
    }

    public function onBreak(Item $item, ?Player $player = null): bool{
        $parent = parent::onBreak($item, $player);
        $started = false;

        for($y = 0; $y < $this->pos->getWorld()->getMaxY(); $y++){
            $block = $this->pos->getWorld()->getBlockAt($this->pos->x, $y, $this->pos->z);

            if($this->pos->y === $y){
                break;
            }
            if(!$block instanceof WeepingVinesBlock && $started){
                break;
            }
            $started = true;
            $this->pos->getWorld()->useBreakOn($block->pos);
        }
        return $parent;
    }

    public function onNearbyBlockChange(): void{
        $block = $this->getSide(Facing::UP);

        if($block instanceof Air){
            $this->pos->getWorld()->useBreakOn($this->pos);
        }
    }

    public function onRandomTick(): void{
        if($this->age !== 15){
            if($this->pos->y === 0){
                $this->age = 15;
                return;
            }
            $b = $this->pos->getWorld()->getBlockAt($this->pos->x, $this->pos->y - 1, $this->pos->z);

            if($b->getId() === BlockLegacyIds::AIR){
                $newAge = $this->age + 1;

                $ev = new BlockGrowEvent($b, BlockFactory::getInstance()->get(BlockVanilla::WEEPING_VINES, $newAge > 15 ? 15 : $newAge));
                $ev->call();
                if($ev->isCancelled()){
                    return;
                }
                $this->pos->getWorld()->setBlock($b->pos, $ev->getNewState());
            }
        }
    }

    public function ticksRandomly(): bool{
        return true;
    }

    public function getDrops(Item $item): array{
        $failed = true;
        $chance = 33;

        if($item->hasEnchantment($enchantment = EnchantmentIdMap::getInstance()->fromId(EnchantmentIds::FORTUNE))){
            $chance = $chance + (22 * $item->getEnchantmentLevel($enchantment));
        }
        if(mt_rand(0, 100) >= $chance){
            $failed = false;
        }
        if($failed){
            return [];
        }
        return [$this->asItem()];
    }

    public function getAge(): int{
        return $this->age;
    }

    public function getStateBitmask(): int{
        return 0b1111;
    }

    protected function writeStateToMeta(): int{
        return $this->age;
    }

    public function readStateFromData(int $id, int $stateMeta): void{
        $this->age = BlockDataSerializer::readBoundedInt("age", $stateMeta, 0, 15);
    }
}