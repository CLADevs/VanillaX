<?php

namespace CLADevs\VanillaX\network\protocol;

use CLADevs\VanillaX\VanillaX;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\PlayerNetworkSessionAdapter;
use pocketmine\network\mcpe\protocol\MobEquipmentPacket;
use pocketmine\network\mcpe\protocol\types\ContainerIds;
use pocketmine\Player;
use pocketmine\Server;
use ReflectionClass;
use ReflectionException;

class MobEquipmentPacketX extends MobEquipmentPacket{

    /**
     * @param NetworkSession $handler
     * @return bool
     * @throws ReflectionException
     */
    public function handle(NetworkSession $handler): bool{
        if($handler instanceof PlayerNetworkSessionAdapter && $this->windowId === ContainerIds::OFFHAND){
            //VERY hacky way
            $rc = new ReflectionClass(PlayerNetworkSessionAdapter::class);
            $var = $rc->getProperty("player");
            $var->setAccessible(true);
            $player = $var->getValue($handler);

            if($player instanceof Player){
                if(!$player->spawned || !$player->isAlive()){
                    return true;
                }
                $inventory = VanillaX::getInstance()->getSessionManager()->get($player)->getOffHandInventory();
                $item = $inventory->getItem($this->hotbarSlot);

                if(!$item->equals($this->item->getItemStack())){
                    Server::getInstance()->getLogger()->debug("Tried to equip " . $this->item->getItemStack() . " but have " . $item . " in target slot");
                    $inventory->sendContents($player);
                    return false;
                }

                $inventory->equipItem($this->hotbarSlot);
                $player->setUsingItem(false);
                return true;
            }
        }
        return parent::handle($handler);
    }
}