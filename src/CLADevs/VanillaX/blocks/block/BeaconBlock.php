<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\BeaconTile;
use CLADevs\VanillaX\inventories\types\BeaconInventory;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class BeaconBlock extends Transparent{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::BEACON, 0, null, BeaconTile::class),"Beacon", new BlockBreakInfo(3));
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($player instanceof Player){
            $player->setCurrentWindow(new BeaconInventory($this->pos));
        }
        return true;
    }
}