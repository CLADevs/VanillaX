<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\block\BlockIds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UIInventorySlotOffset;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class TradeInventory extends FakeBlockInventory{

    public function __construct(Vector3 $holder){
        parent::__construct($holder, 3, BlockIds::AIR, WindowTypes::TRADING);
    }

    public function getName(): string{
        return "Trade";
    }

    public function getDefaultSize(): int{
        return 3;
    }

    public function getNetworkType(): int{
        return WindowTypes::TRADING;
    }

    public function handlePacket(Player $player, DataPacket $packet): bool{
        if($packet instanceof InventoryTransactionPacket){
            $actions = $packet->actions;

            if(count($actions) < 1){
                //var_dump("Null actions");
                return true;
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
        return true;
    }
}