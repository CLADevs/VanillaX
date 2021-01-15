<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\inventory\ContainerInventory;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UIInventorySlotOffset;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class TradeInventory extends ContainerInventory{

    public function getName(): string{
        return "Trade";
    }

    public function getDefaultSize(): int{
        return 3;
    }

    public function getNetworkType(): int{
        return WindowTypes::TRADING;
    }

    public function handlePacket(Player $player, InventoryTransactionPacket $packet): void{
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
                if(array_key_exists($slot, UIInventorySlotOffset::TRADE2_INGREDIENT)){
                    $slot = UIInventorySlotOffset::TRADE2_INGREDIENT[$slot];
                }
            }
            $inv->setItem($slot, $action->newItem);
        }
    }
}