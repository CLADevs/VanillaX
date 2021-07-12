<?php

namespace CLADevs\VanillaX\network\protocol\types\inventory;

use CLADevs\VanillaX\network\protocol\types\NetworkInventoryActionX;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;
use pocketmine\network\mcpe\protocol\types\inventory\NormalTransactionData;

class NormalTransactionDataX extends NormalTransactionData{

    /**
     * @return NetworkInventoryActionX[]
     */
    public function getAllActions(): array{
        return $this->actions;
    }

    public function customDecode(PacketSerializer $stream) : void{
        $actionCount = $stream->getUnsignedVarInt();
        for($i = 0; $i < $actionCount; ++$i){
            $this->actions[] = (new NetworkInventoryActionX())->read($stream);
        }
        $this->decodeData($stream);
    }
}