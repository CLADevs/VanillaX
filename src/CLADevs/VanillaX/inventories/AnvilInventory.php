<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\block\BlockIds;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AnvilDamagePacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\FilterTextPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\types\inventory\UIInventorySlotOffset;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class AnvilInventory extends FakeBlockInventory{

    private string $currentName = "";

    public function __construct(Vector3 $holder){
        parent::__construct($holder, 2, BlockIds::AIR, WindowTypes::ANVIL);
    }

    public function onClose(Player $who): void{
        parent::onClose($who);

        foreach($this->getContents() as $item){
            $who->dropItem($item);
        }
        $this->clearAll();
    }

    public function handlePacket(Player $player, DataPacket $packet): bool{
        if($packet instanceof InventoryTransactionPacket){
            $actions = $packet->trData->getActions();

            if(count($actions) < 1){
                //var_dump("Null actions");
                return true;
            }
            foreach($actions as $key => $action){
                $slot = $action->inventorySlot;
                $inv = $this;
                $item = $action->newItem->getItemStack();

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
        }elseif($packet instanceof AnvilDamagePacket){ //TODO Change this to transaction
            $this->onSuccess($player);
        }
        return true;
    }

    public function onSuccess(Player $player): void{
        $this->setItem(0, ItemFactory::get(ItemIds::AIR));
        $this->setItem(1, ItemFactory::get(ItemIds::AIR));
    }

    private function onNameChange(FilterTextPacket $packet): void{
        $this->currentName = $packet->getText();
    }
}