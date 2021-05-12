<?php

namespace CLADevs\VanillaX\blocks\types;

use CLADevs\VanillaX\inventories\EnchantInventory;
use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use pocketmine\tile\EnchantTable;
use pocketmine\tile\Tile;

class EnchantmentTableBlock extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::ENCHANTMENT_TABLE, $meta);
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        Tile::createTile(Tile::ENCHANT_TABLE, $this->getLevel(), EnchantTable::createNBT($this));
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            if(!$this->getLevel()->getTile($this) instanceof EnchantTable){
                Tile::createTile(Tile::ENCHANT_TABLE, $this->getLevel(), EnchantTable::createNBT($this));
            }
            $player->addWindow(new EnchantInventory($this), WindowTypes::ENCHANTMENT);
        }
        return true;
    }

    public function getBlastResistance(): float{
        return 1200;
    }

    public function getHardness(): float{
        return 5;
    }

}