<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\BeaconTile;
use CLADevs\VanillaX\blocks\utils\TileUtils;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\inventories\types\BeaconInventory;
use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class BeaconBlock extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::BEACON, $meta, "Beacon");
    }

    public function getHardness(): float{
        return 3;
    }

    public function getFlammability(): int{
        return 15;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        TileUtils::generateTile($this, TileVanilla::BEACON, BeaconTile::class);
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            $player->addWindow(new BeaconInventory($this));
        }
        return false;
    }
}