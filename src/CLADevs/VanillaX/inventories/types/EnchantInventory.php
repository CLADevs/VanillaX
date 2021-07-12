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

    public function onClose(Player $who): void{
        parent::onClose($who);

        foreach($this->getContents() as $item){
            $who->dropItem($item);
        }
        $this->clearAll();
    }

//    public function handlePacket(Player $player, ServerboundPacket $packet): bool{
//        if($packet instanceof InventoryTransactionPacket){
//            if($packet->trData instanceof NormalTransactionData){
//                foreach($packet->trData->getActions() as $act){
//                    $inventorySlot = UIInventorySlotOffset::ENCHANTING_TABLE[$act->inventorySlot] ?? null;
//                    $newItem = TypeConverter::getInstance()->netItemStackToCore($act->newItem->getItemStack());
//                    $oldItem = TypeConverter::getInstance()->netItemStackToCore($act->oldItem->getItemStack());
//
//                    if($inventorySlot !== null){
//                        $this->setItem($inventorySlot, $newItem);
//                    }else{
//                        $player->getInventory()->setItem($act->inventorySlot, $newItem);
//                    }
//                    if($act->windowId === NetworkInventoryAction::SOURCE_TYPE_ENCHANT_OUTPUT){
//                        $this->onSuccess($player, $oldItem);
//                    }
//                }
//            }
//        }
//        return true;
//    }

    /**
     * @param Player $player, returns player who successfully enchanted their item
     * @param Item $item, returns a new item after its enchanted
     */
//    public function onSuccess(Player $player, Item $item): void{
//        $ingredient = $this->getItem(1);
//        $ingredient->setCount($ingredient->getCount() - 1);
//        $this->setItem(1, $ingredient);
//    }
}