<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class EnchantInventory extends FakeBlockInventory{

    public function __construct(Vector3 $holder){
        parent::__construct($holder, 2, BlockIds::AIR, WindowTypes::ENCHANTMENT);
    }

    public function onClose(Player $who): void{
        parent::onClose($who);

        foreach($this->getContents() as $item){
            $who->dropItem($item);
        }
        $this->clearAll();
    }

    /**
     * @param Player $player, returns player who successfully enchanted their item
     * @param Item $item, returns a new item after its enchanted
     */
    public function onSuccess(Player $player, Item $item): void{
    }
}