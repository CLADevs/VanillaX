<?php

namespace CLADevs\VanillaX\network\protocol;

use CLADevs\VanillaX\network\protocol\types\inventory\NormalTransactionDataX;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\PacketDecodeException;
use pocketmine\network\mcpe\protocol\PacketHandlerInterface;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\inventory\InventoryTransactionChangedSlotsHack;
use pocketmine\network\mcpe\protocol\types\inventory\MismatchTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\ReleaseItemTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemOnEntityTransactionData;
use pocketmine\network\mcpe\protocol\types\inventory\UseItemTransactionData;

class InventoryTransactionPacketX extends InventoryTransactionPacket{

    public bool $isRepairItemPart = false;

    protected function decodePayload(PacketSerializer $in): void{
        $this->requestId = $in->readGenericTypeNetworkId();
        $this->requestChangedSlots = [];
        if($this->requestId !== 0){
            for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
                $this->requestChangedSlots[] = InventoryTransactionChangedSlotsHack::read($in);
            }
        }

        $transactionType = $in->getUnsignedVarInt();

        switch($transactionType){
            case self::TYPE_NORMAL:
                $this->trData = new NormalTransactionDataX();
                break;
            case self::TYPE_MISMATCH:
                $this->trData = new MismatchTransactionData();
                break;
            case self::TYPE_USE_ITEM:
                $this->trData = new UseItemTransactionData();
                break;
            case self::TYPE_USE_ITEM_ON_ENTITY:
                $this->trData = new UseItemOnEntityTransactionData();
                break;
            case self::TYPE_RELEASE_ITEM:
                $this->trData = new ReleaseItemTransactionData();
                break;
            default:
                throw new PacketDecodeException("Unknown transaction type $transactionType");
        }

        if($transactionType !== self::TYPE_NORMAL){
            $this->trData->decode($in);
        }else{
            $this->trData->customDecode($in);
        }
    }

    public function handle(PacketHandlerInterface $handler): bool{
        if($this->trData instanceof NormalTransactionDataX){
            foreach($this->trData->getAllActions() as $tr){
                $tr->createInventoryAction($handler);
            }
        }
        return $handler->handleInventoryTransaction($this);
    }
}