<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\DispenserTile;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Opaque;
use pocketmine\block\utils\FacesOppositePlacingPlayerTrait;
use pocketmine\block\utils\NormalHorizontalFacingInMetadataTrait;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class DispenserBlock extends Opaque{
    use FacesOppositePlacingPlayerTrait;
    use NormalHorizontalFacingInMetadataTrait;

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::DISPENSER, 0, null, DispenserTile::class), "Dispenser", new BlockBreakInfo(3.5));
    }

    public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null): bool{
        if($player !== null){
            $tile = $this->getPos()->getWorld()->getTile($this->getPos());

            if($tile instanceof DispenserTile){
                $player->setCurrentWindow($tile->getInventory());
            }
        }
        return true;
    }
}