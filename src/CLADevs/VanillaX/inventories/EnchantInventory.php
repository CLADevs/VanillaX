<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\network\mcpe\protocol\types\inventory\UIInventorySlotOffset;
use pocketmine\network\mcpe\protocol\types\NetworkInventoryAction;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class EnchantInventory extends \pocketmine\inventory\EnchantInventory{

    /**
     * @param Player $player
     * @param NetworkInventoryAction[] $actions
     */
    public function handleTransaction(Player $player, array $actions): void{
        if(count($actions) < 1){
            //var_dump("Null actions");
            return;
        }
        $actions = $this->createInventoryAction($player, $actions);
        foreach($actions as $action){
            $inv = $action->getInventory();
            $inv->setItem($action->getSlot(), $action->getTargetItem());
        }
    }

    /**
     * @param Player $player
     * @param NetworkInventoryAction[] $actions
     * @return SlotChangeAction[]
     */
    public function createInventoryAction(Player $player, array $actions): array{
        foreach($actions as $key => $action){
            $slot = $action->inventorySlot;
            if(array_key_exists($slot, UIInventorySlotOffset::ENCHANTING_TABLE)){
                $slot = UIInventorySlotOffset::ENCHANTING_TABLE[$slot];
            }
            $inv = $this;
            if($action->windowId === WindowTypes::CONTAINER){
                $inv = $player->getInventory();
            }
            $actions[$key] = new SlotChangeAction($inv, $slot, $action->oldItem, $action->newItem);
        }
        return $actions;
    }
}