<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UIInventorySlotOffset;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class EnchantInventory extends \pocketmine\inventory\EnchantInventory{

    /**
     * @param Player $player
     * @param PlayerActionPacket|InventoryTransactionPacket $packet
     */
    public function handlePacket(Player $player, $packet): void{
        if($packet instanceof InventoryTransactionPacket){
            $actions = $packet->actions;

            if(count($actions) < 1){
                //var_dump("Null actions");
                return;
            }
            foreach($actions as $key => $action){
                $slot = $action->inventorySlot;
                $inv = $this;
                if($action->windowId === WindowTypes::CONTAINER){
                    $inv = $player->getInventory();
                }else{
                    if(array_key_exists($slot, UIInventorySlotOffset::ENCHANTING_TABLE)){
                        $slot = UIInventorySlotOffset::ENCHANTING_TABLE[$slot];
                    }
                }
                $inv->setItem($slot, $action->newItem);
            }
        }elseif($packet->action === PlayerActionPacket::ACTION_SET_ENCHANTMENT_SEED){
            $this->onSuccess($player);
        }
    }

    public function onSuccess(Player $player): void{
    }
}