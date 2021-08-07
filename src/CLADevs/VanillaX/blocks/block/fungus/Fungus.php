<?php

namespace CLADevs\VanillaX\blocks\block\fungus;

use CLADevs\VanillaX\blocks\utils\BlockVanilla;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Transparent;
use pocketmine\item\Fertilizer;
use pocketmine\item\Item;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class Fungus extends Transparent implements NonAutomaticCallItemTrait{

    private int $mainBlockId;

    public function __construct(int $mainBlockId, BlockIdentifier $idInfo, string $name, BlockBreakInfo $breakInfo){
        parent::__construct($idInfo, $name, $breakInfo);
        $this->mainBlockId = $mainBlockId;
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if(!in_array($this->getSide(Facing::DOWN)->getId(), [BlockLegacyIds::GRASS, BlockLegacyIds::DIRT, BlockLegacyIds::PODZOL, BlockLegacyIds::FARMLAND, BlockVanilla::WARPED_NYLIUM, BlockVanilla::CRIMSON_NYLIUM, BlockLegacyIds::MYCELIUM, BlockLegacyIds::SOUL_SAND])){
            return false;
        }
        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($item instanceof Fertilizer){
            $item->pop();

            if($this->getSide(Facing::DOWN)->getId() === $this->mainBlockId){
                //TODO grow tree
            }
        }
        return parent::onInteract($item, $face, $clickVector, $player);
    }

    public function getMainBlockId(): int{
        return $this->mainBlockId;
    }
}