<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\blocks\TileIdentifiers;
use CLADevs\VanillaX\blocks\tiles\JukeboxTile;
use CLADevs\VanillaX\items\types\MusicDiscItem;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\block\Solid;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\tile\Tile;

class JukeboxBlock extends Solid{

    public function __construct(int $meta = 0){
        parent::__construct(BlockIds::JUKEBOX, $meta);
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        $this->meta = $faces[$face] ?? $face;
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        Tile::createTile(TileIdentifiers::JUKEBOX, $this->getLevel(), JukeboxTile::createNBT($this));
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        $tile = $player->getLevel()->getTile($this);

        if($player !== null && $tile instanceof JukeboxTile){
            if($item instanceof MusicDiscItem && $tile->getRecordItem() === null){
                $tile->insertTrack($player, $item);
            }else{
                $tile->removeTrack();
            }
        }
        return true;
    }

    public function getHardness(): float{
        return 2;
    }

    public function getBlastResistance(): float{
        return 6;
    }

    public function getFlameEncouragement(): int{
        return 5;
    }

    public function getFlammability(): int{
        return 10;
    }
}