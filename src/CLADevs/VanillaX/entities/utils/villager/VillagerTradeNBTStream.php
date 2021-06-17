<?php

namespace CLADevs\VanillaX\entities\utils\villager;

use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\NamedTag;

class VillagerTradeNBTStream{

    private NetworkLittleEndianNBTStream $stream;

    /** @var NamedTag[] */
    private array $namedTag = [];
    /** @var VillagerOffer[] */
    private array $offers = [];

    public function __construct(){
        $this->stream = new NetworkLittleEndianNBTStream();
    }

    /**
     * @return VillagerOffer[]
     */
    public function getOffers(): array{
        return $this->offers;
    }

    /**
     * @return NamedTag[]
     */
    public function getNamedTag(): array{
        return $this->namedTag;
    }

    /**
     * @param VillagerOffer|VillagerOffer[] $offer
     */
    public function addOffer($offer): void{
        if(!is_array($offer)){
            $offer = [$offer];
        }
        foreach($offer as $i){
            $this->offers[] = $i;
            $this->namedTag[] = $i->asItem();
        }
    }

    public function initialize(): void{
        $this->stream->writeTag(new CompoundTag("", [new ListTag("Recipes", $this->namedTag)]));
    }

    public function getStream(): NetworkLittleEndianNBTStream{
        return $this->stream;
    }

    public function getBuffer(): string{
        return $this->stream->buffer;
    }
}