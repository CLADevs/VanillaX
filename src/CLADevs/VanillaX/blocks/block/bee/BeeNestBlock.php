<?php

namespace CLADevs\VanillaX\blocks\block\bee;

use CLADevs\VanillaX\blocks\BlockIds;
use CLADevs\VanillaX\blocks\utils\AnyFacingTrait;
use CLADevs\VanillaX\items\LegacyItemIds;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Block;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\item\Item;
use pocketmine\item\ToolTier;
use pocketmine\math\Facing;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\BlockTransaction;

class BeeNestBlock extends Opaque implements NonAutomaticCallItemTrait{
    use AnyFacingTrait;

    public function __construct(int $meta = 0){
        parent::__construct(new BlockIdentifier(BlockIds::BEE_NEST, $meta, LegacyItemIds::BEE_NEST), "Bee Nest", new BlockBreakInfo(0.3, BlockToolType::AXE, ToolTier::WOOD()->getHarvestLevel(), 0.3));
    }

    public function place(BlockTransaction $tx, Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
        $this->facing = match($player->getHorizontalFacing()){
            Facing::NORTH => Facing::DOWN,
            Facing::EAST => Facing::UP,
            Facing::SOUTH => Facing::NORTH,
            Facing::WEST => Facing::SOUTH,
            default => $face
        };
        return parent::place($tx, $item, $blockReplace, $blockClicked, $face, $clickVector, $player);
    }
}