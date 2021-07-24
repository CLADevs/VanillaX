<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\world\sounds\BellSound;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class BellBlock extends Opaque{

    //TODO facing
    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::BELL, 0, ItemIds::BELL), "Bell", new BlockBreakInfo(5, BlockToolType::PICKAXE, 0, 5));
    }
    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        $this->pos->getWorld()->addSound($this->pos, new BellSound());
        return parent::onInteract($item, $face, $clickVector, $player);
    }
}