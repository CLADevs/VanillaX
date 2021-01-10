<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\network\mcpe\protocol\FilterTextPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UIInventorySlotOffset;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class AnvilInventory extends \pocketmine\inventory\AnvilInventory{

    private string $currentName = "";

    /**
     * @param Player $player
     * @param InventoryTransactionPacket|FilterTextPacket $packet
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
                $item = $action->newItem;

                if($action->windowId === WindowTypes::CONTAINER){
                    $inv = $player->getInventory();
                }else{
                    if(array_key_exists($slot, UIInventorySlotOffset::ANVIL)){
                        $slot = UIInventorySlotOffset::ANVIL[$slot];

                        if($slot === 0){
                            $this->currentName = $item->getId() === ItemIds::AIR ? "" : $item->getName();
                        }
                    }
                }
                $inv->setItem($slot, $item);
            }
        }elseif($packet instanceof FilterTextPacket){
            $this->onNameChange($packet);
        }else{
            $this->onSuccess($player);
        }
    }

    public function onSuccess(Player $player): void{
        $this->setItem(0, ItemFactory::get(ItemIds::AIR));
        $this->setItem(1, ItemFactory::get(ItemIds::AIR));
    }

    private function onNameChange(FilterTextPacket $packet): void{
        $this->currentName = $packet->getText();
    }
}