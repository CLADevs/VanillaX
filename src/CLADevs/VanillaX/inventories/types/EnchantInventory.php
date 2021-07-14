<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;
use pocketmine\world\Position;

class EnchantInventory extends FakeBlockInventory{

    public function __construct(Position $holder){
        parent::__construct($holder, 2, BlockLegacyIds::AIR, WindowTypes::ENCHANTMENT);
    }

    /**
     * @param Player $player, returns player who successfully enchanted their item
     * @param Item $item, returns a new item after its enchanted
     */
    public function onSuccess(Player $player, Item $item): void{
    }
}