<?php

namespace CLADevs\VanillaX\inventories;

use pocketmine\block\BlockIds;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PlayerActionPacket;
use pocketmine\network\mcpe\protocol\PlayerEnchantOptionsPacket;
use pocketmine\network\mcpe\protocol\types\Enchant;
use pocketmine\network\mcpe\protocol\types\EnchantOption;
use pocketmine\network\mcpe\protocol\types\inventory\UIInventorySlotOffset;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class EnchantInventory extends FakeBlockInventory{

    public function __construct(Vector3 $holder){
        parent::__construct($holder, 2, BlockIds::AIR, WindowTypes::ENCHANTMENT);
    }

    public function onOpen(Player $who): void{
        parent::onOpen($who);
        $options = [
            new EnchantOption(10, 1, [new Enchant(Enchantment::LOYALTY, 1)], [new Enchant(Enchantment::LOYALTY, 1)], [new Enchant(Enchantment::LOYALTY ,1)], "LOL",1 )
        ];
        $pk = PlayerEnchantOptionsPacket::create($options);
        $who->dataPacket($pk);
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
                    if(array_key_exists($slot, UIInventorySlotOffset::ENCHANTING_TABLE)){
                        $slot = UIInventorySlotOffset::ENCHANTING_TABLE[$slot];
                    }
                }
                $inv->setItem($slot, $action->newItem);
            }
        }elseif($packet instanceof PlayerActionPacket && $packet->action === PlayerActionPacket::ACTION_SET_ENCHANTMENT_SEED){
            $this->onSuccess($player);
        }
        return true;
    }

    public function onSuccess(Player $player): void{
    }
}