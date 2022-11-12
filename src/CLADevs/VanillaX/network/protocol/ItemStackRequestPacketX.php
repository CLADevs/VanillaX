<?php

namespace CLADevs\VanillaX\network\protocol;

use CLADevs\VanillaX\network\types\stackrequest\ItemStackRequestX;
use pocketmine\network\mcpe\protocol\ItemStackRequestPacket;
use pocketmine\network\mcpe\protocol\serializer\PacketSerializer;

class ItemStackRequestPacketX extends ItemStackRequestPacket{

    /** @var ItemStackRequestX[] */
    private array $requests;

    /**
     * @generate-create-func
     * @param ItemStackRequestX[] $requests
     */
    public static function create(array $requests) : self{
        $result = new self;
        $result->requests = $requests;
        return $result;
    }

    /** @return ItemStackRequestX[] */
    public function getRequests(): array{ return $this->requests; }

    protected function decodePayload(PacketSerializer $in): void{
        $this->requests = [];
        for($i = 0, $len = $in->getUnsignedVarInt(); $i < $len; ++$i){
            $this->requests[] = ItemStackRequestX::read($in);
        }
    }

    protected function encodePayload(PacketSerializer $out): void{
        $out->putUnsignedVarInt(count($this->requests));
        foreach($this->requests as $request){
            $request->write($out);
        }
    }
}