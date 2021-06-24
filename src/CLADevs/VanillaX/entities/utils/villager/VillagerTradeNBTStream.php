<?php

namespace CLADevs\VanillaX\entities\utils\villager;

use pocketmine\nbt\NetworkLittleEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\NamedTag;

class VillagerTradeNBTStream{

    const TAG_RECIPES = "Recipes";
    const TAG_TIER_EXP_REQUIREMENT = "TierExpRequirements";

    private VillagerProfession $profession;
    private NetworkLittleEndianNBTStream $stream;

    /** @var NamedTag[] */
    private array $namedTag = [];
    /** @var VillagerOffer[] */
    private array $offers = [];

    public function __construct(VillagerProfession $profession){
        $this->profession = $profession;
        $this->stream = new NetworkLittleEndianNBTStream();
    }

    public function initialize(): void{
        $tierList = [];
        for($i = 0; $i <= 4; $i++){
            $tierList[] = new CompoundTag("", [new IntTag(strval($i), $this->profession->getProfessionExp($i))]);
        }
        $this->stream->writeTag(new CompoundTag("", [
            new ListTag(self::TAG_RECIPES, $this->namedTag),
            new ListTag(self::TAG_TIER_EXP_REQUIREMENT, $tierList)
        ]));
    }

    public function getProfession(): VillagerProfession{
        return $this->profession;
    }

    /**
     * @param int $tier
     * @param VillagerOffer|VillagerOffer[] $offer
     */
    public function addOffer(int $tier, $offer): void{
        if(!is_array($offer)){
            $offer = [$offer];
        }
        foreach($offer as $i){
            $nbt = $i->asItem();

            if($nbt instanceof NamedTag){
                $nbt->setInt(VillagerOffer::TAG_TIER, $tier);
                $this->namedTag[] = $nbt;
                $this->offers[] = $i;
            }
        }
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

    public function getStream(): NetworkLittleEndianNBTStream{
        return $this->stream;
    }

    public function getBuffer(): string{
        return $this->stream->buffer;
    }
}