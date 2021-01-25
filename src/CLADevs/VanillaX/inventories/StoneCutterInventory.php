<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\block\BlockIds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

class StoneCutterInventory extends FakeBlockInventory{

    public function __construct(Vector3 $holder){
        parent::__construct($holder, 4, BlockIds::AIR, WindowTypes::STONECUTTER);
    }
}