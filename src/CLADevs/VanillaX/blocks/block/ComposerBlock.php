<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\inventories\InventoryManager;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use CLADevs\VanillaX\world\sounds\ComposterEmptySound;
use CLADevs\VanillaX\world\sounds\ComposterFillSound;
use CLADevs\VanillaX\world\sounds\ComposterFillSuccessSound;
use CLADevs\VanillaX\world\sounds\ComposterReadySound;
use InvalidArgumentException;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Transparent;
use pocketmine\block\utils\BlockDataSerializer;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\HappyVillagerParticle;

class ComposerBlock extends Transparent implements NonAutomaticCallItemTrait{

    protected int $level = 0;

    public function __construct(int $meta = 0){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::COMPOSTER, $meta, ItemIds::COMPOSTER), "Composter", new BlockBreakInfo(0.6, BlockToolType::AXE));
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        $world = $this->position->getWorld();
        $particle = false;

        if($this->level < 7){
            if(($chance = InventoryManager::getInstance()->getComposterChance($item)) > 0 && mt_rand(0, 100) < $chance){
                $this->level++;
                $world->setBlock($this->position, $this);

                if($this->level === 7){
                    $world->scheduleDelayedBlockUpdate($this->position, 20);
                }
                if(!$player->isCreative()) $item->pop();
                $world->addSound($this->position, new ComposterFillSuccessSound());
            }else{
                $world->addSound($this->position, new ComposterFillSound());
            }
            $particle = true;
        }elseif($this->level === 8){
            $this->level = 0;

            $world->setBlock($this->position, $this);
            $world->dropItem(clone $this->position->add(0.5, 0.5, 0.5), VanillaItems::BONE_MEAL());
            $world->addSound($this->position, new ComposterEmptySound());
            $particle = true;
        }
        if($particle){
            for($i = 0; $i <= 12; $i++){
                $world->addParticle(clone $this->position->add(mt_rand(1, 10) / 10, mt_rand(1, 10) / 10, mt_rand(1, 10) / 10), new HappyVillagerParticle());
            }
        }
        return true;
    }

    public function onScheduledUpdate(): void{
        if($this->level === 7){
            $this->level = 8;
            $this->position->world->setBlock($this->position, $this);
            $this->position->world->addSound($this->position, new ComposterReadySound());
        }
    }

    public function getLevel(): int{
        return $this->level;
    }

    public function setLevel(int $level): self{
        if($level < 0 || $level > 8){
            throw new InvalidArgumentException("Level must be in range 0-8");
        }
        $this->level = $level;
        return $this;
    }

    protected function writeStateToMeta(): int{
        return $this->level;
    }

    public function readStateFromData(int $id, int $stateMeta): void{
        $this->level = BlockDataSerializer::readBoundedInt("level", $stateMeta, 0, 8);
    }

    public function getStateBitmask(): int{
        return 0b1111;
    }

    public function getFlammability(): int{
        return 5;
    }
}