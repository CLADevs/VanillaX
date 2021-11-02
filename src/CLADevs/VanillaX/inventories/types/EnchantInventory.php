<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\inventories\FakeBlockInventory;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
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

    public function handlePacket(Player $player, DataPacket $packet): bool{
        if($packet instanceof ActorEventPacket && $packet->event === ActorEventPacket::PLAYER_ADD_XP_LEVELS){
            if(!$player->isCreative()){
                $player->setXpLevel($player->getXpLevel() - abs($packet->data)); //TODO custom enchanting table enchantment manager
            }
        }
        return parent::handlePacket($player, $packet);
    }

    /**
     * @param Player $player, returns player who successfully enchanted their item
     * @param Item $item, returns a new item after its enchanted
     */
    public function onSuccess(Player $player, Item $item): void{
    }
}