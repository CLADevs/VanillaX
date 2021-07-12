<?php

namespace CLADevs\VanillaX\blocks\block\redstone;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Transparent;
use pocketmine\item\ItemIds;

class RedstoneRepeater extends Transparent implements NonAutomaticCallItemTrait{

    private bool $powered;

    public function __construct(bool $powered){
        $id = $powered ? BlockLegacyIds::POWERED_REPEATER : BlockLegacyIds::UNPOWERED_REPEATER;
        $name = ($powered ? "Powered" : "Unpowered") . " Repeater";
        parent::__construct(new BlockIdentifier($id, 0, ItemIds::REPEATER), $name, new BlockBreakInfo(0));
        $this->powered = $powered;
    }

    public function isPowered(): bool{
        return $this->powered;
    }
}