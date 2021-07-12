<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\world\Position;

class DropperInventory extends FakeBlockInventory{

    public function __construct(Position $holder){
        parent::__construct($holder, 9, BlockLegacyIds::AIR, WindowTypes::DROPPER);
    }
}