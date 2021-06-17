<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\utils\TileUtils;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\inventories\types\EnchantInventory;
use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;
use pocketmine\tile\EnchantTable;

class EnchantmentTableBlock extends Transparent{

    public function __construct(int $meta = 0){
        parent::__construct(self::ENCHANTMENT_TABLE, $meta);
    }

    public function getBlastResistance(): float{
        return 1200;
    }

    public function getHardness(): float{
        return 5;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        TileUtils::generateTile($this, TileVanilla::ENCHANT_TABLE, EnchantTable::class);
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            $player->addWindow(new EnchantInventory($this), WindowTypes::ENCHANTMENT);
        }
        return true;
    }
}