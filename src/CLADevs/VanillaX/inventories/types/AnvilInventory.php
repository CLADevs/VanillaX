<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\network\protocol\FilterTextPacketX;
use pocketmine\block\BlockIds;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\DataPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\types\WindowTypes;
use pocketmine\Player;

class AnvilInventory extends FakeBlockInventory{

    public function __construct(Vector3 $holder){
        parent::__construct($holder, 2, BlockIds::AIR, WindowTypes::ANVIL, null);
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
                $player->setXpLevel($player->getXpLevel() - abs($packet->data));
            }
        }elseif($packet instanceof FilterTextPacketX){
            $player->dataPacket(FilterTextPacketX::create($packet->getText(), true));
        }
        return true;
    }

    /**
     * @param Player $player, returns player who successfully repaired/renamed their item
     * @param Item $item, returns a new item after its repaired/renamed
     */
    public function onSuccess(Player $player, Item $item): void{
        $player->getLevel()->broadcastLevelSoundEvent($this->getHolder(), LevelSoundEventPacket::SOUND_RANDOM_ANVIL_USE);
    }
}