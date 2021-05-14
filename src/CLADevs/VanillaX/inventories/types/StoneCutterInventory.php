<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\block\BlockIds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\WindowTypes;

class StoneCutterInventory extends FakeBlockInventory{

    public function __construct(Vector3 $holder){
        parent::__construct($holder, 1, BlockIds::AIR, WindowTypes::STONECUTTER);
    }
}