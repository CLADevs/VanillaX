<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\block\BlockIds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class BeaconInventory extends FakeBlockInventory{

    public function __construct(Vector3 $holder){
        parent::__construct($holder, 1, BlockIds::AIR, WindowTypes::BEACON);
    }

    public function onClose(Player $who): void{
        parent::onClose($who);

        foreach($this->getContents() as $item){
            $who->dropItem($item);
        }
        $this->clearAll();
    }
}