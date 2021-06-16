<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\blocks\BlockIdentifiers;
use JavierLeon9966\ExtendedBlocks\block\PlaceholderTrait;
use pocketmine\block\BlockToolType;
use pocketmine\block\Solid;
use pocketmine\item\TieredTool;

class AncientDebrisBlock extends Solid {
    use PlaceholderTrait;

    protected $id = BlockIdentifiers::ANCIENT_DEBRIS;

    public function __construct(int $meta = 0)
    {
        parent::__construct(BlockIdentifiers::ANCIENT_DEBRIS, $meta, "Ancient Debris");
    }

    public function getBlastResistance(): float
    {
        return 1200;
    }

    public function getHardness(): float
    {
        return 30;
    }

    public function getToolType(): int
    {
        return BlockToolType::TYPE_PICKAXE;
    }

    public function getToolHarvestLevel(): int
    {
        return TieredTool::TIER_DIAMOND;
    }

    // TODO: Add extra data
}