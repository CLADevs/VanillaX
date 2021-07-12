<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\JukeboxTile;
use CLADevs\VanillaX\items\types\MusicDiscItem;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\Opaque;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class JukeboxBlock extends Opaque{
    //TODO tile

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::JUKEBOX, 0), "Jukebox", new BlockBreakInfo(2, BlockToolType::AXE, 0, 6));
    }

    public function getFlameEncouragement(): int{
        return 5;
    }

    public function getFlammability(): int{
        return 10;
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        $tile = $player->getWorld()->getTile($this->getPos());

        if($player !== null && $tile instanceof JukeboxTile){
            if($item instanceof MusicDiscItem && $tile->getRecordItem() === null){
                $tile->insertTrack($player, $item);
            }else{
                $tile->removeTrack();
            }
        }
        return true;
    }
}