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

class BeehiveBlock extends Opaque implements NonAutomaticCallItemTrait{
    use AnyFacingTrait;

    public function __construct(int $meta = 0){
        parent::__construct(new BlockIdentifier(BlockIds::BEEHIVE, $meta, LegacyItemIds::BEEHIVE), "Beehive", new BlockBreakInfo(0.6, BlockToolType::AXE, ToolTier::STONE()->getHarvestLevel(), 0.6));
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