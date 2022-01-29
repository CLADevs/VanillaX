<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\BlockIds;
use CLADevs\VanillaX\items\LegacyItemIds;
use pocketmine\block\Air;
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

class TwistingVinesBlock extends Transparent{

    protected int $age = 0;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockIds::TWISTING_VINES, 0, LegacyItemIds::TWISTING_VINES), "Twisting Vines", BlockBreakInfo::instant());
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($item instanceof Fertilizer){
           $item->pop();
           $height = 0;

           for($y = 1; $y < $this->position->getWorld()->getMaxY(); $y++){
               $block = $this->position->getWorld()->getBlock($this->position->add(0, $y, 0));

               if(!$block instanceof TwistingVinesBlock){
                   continue;
               }
               $height++;
           }
            $lastBlock = $this->position->getWorld()->getBlock($this->position->add(0, $height, 0));

           if($lastBlock instanceof TwistingVinesBlock){
               $lastAge = $lastBlock->getAge();
               $size = mt_rand(1, 6);

               for($i = 1; $i <= $size; $i++){
                   $b = $this->position->getWorld()->getBlock($this->position->add(0, $height + $i, 0));

                   if($b instanceof Air && $b->position->getY() < $b->position->getWorld()->getMaxY()){
                       if($lastAge > 15){
                           $lastAge = 15;
                       }else{
                           $lastAge++;
                       }
                       $this->position->getWorld()->setBlock($b->position, BlockFactory::getInstance()->get(BlockIds::TWISTING_VINES, $lastAge > 15 ? 15 : $lastAge));
                   }else{
                       break;
                   }
               }
           }
           return true;
        }
        return false;
    }

    public function onBreak(Item $item, ?Player $player = null): bool{
        $parent = parent::onBreak($item, $player);

        for($y = 1; $y < $this->position->getWorld()->getMaxY(); $y++){
            $block = $this->position->getWorld()->getBlock($this->position->add(0, $y, 0));

            if(!$block instanceof TwistingVinesBlock){
                break;
            }
            $this->position->getWorld()->useBreakOn($block->position);
        }
        return $parent;
    }

    public function onNearbyBlockChange(): void{
        $block = $this->getSide(Facing::DOWN);

        if($block instanceof Air){
            $this->position->getWorld()->useBreakOn($this->position);
        }
    }

    public function onRandomTick(): void{
        if($this->age !== 15){
            if($this->position->y === ($this->position->getWorld()->getMaxY() - 1)){
                $this->age = 15;
                return;
            }
            $b = $this->position->getWorld()->getBlockAt($this->position->x, $this->position->y + 1, $this->position->z);

            if($b->getId() === BlockLegacyIds::AIR){
                $newAge = $this->age + 1;

                $ev = new BlockGrowEvent($b, BlockFactory::getInstance()->get(BlockIds::TWISTING_VINES, $newAge > 15 ? 15 : $newAge));
                $ev->call();
                if($ev->isCancelled()){
                    return;
                }
                $this->position->getWorld()->setBlock($b->position, $ev->getNewState());
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