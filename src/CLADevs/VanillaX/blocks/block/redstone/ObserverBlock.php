<?php

namespace CLADevs\VanillaX\blocks\block\redstone;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Transparent;
use pocketmine\block\utils\FacesOppositePlacingPlayerTrait;
use pocketmine\block\utils\NormalHorizontalFacingInMetadataTrait;

class ObserverBlock extends Transparent{
    use FacesOppositePlacingPlayerTrait;
    use NormalHorizontalFacingInMetadataTrait;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::OBSERVER, 0), "Observer", new BlockBreakInfo(3.5));
    }
}