<?php

namespace CLADevs\VanillaX\entities\utils\villager;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\Tag;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;

class VillagerTradeNBTStream{

    const TAG_RECIPES = "Recipes";
    const TAG_TIER_EXP_REQUIREMENT = "TierExpRequirements";

    private VillagerProfession $profession;
    private CacheableNbt $stream;

    /** @var Tag[] */
    private array $namedTag = [];
    /** @var VillagerOffer[] */
    private array $offers = [];

    public function __construct(VillagerProfession $profession){
        $this->profession = $profession;
    }

    public function initialize(): void{
        $tierList = [];
        for($i = 0; $i <= 4; $i++){
            $nbt = new CompoundTag();
            $nbt->setInt(strval($i), $this->profession->getProfessionExp($i));
            $tierList[] = $nbt;
        }
        $tag = new CompoundTag();
        $tag->setTag(self::TAG_RECIPES, new ListTag($this->namedTag));
        $tag->setTag(self::TAG_TIER_EXP_REQUIREMENT, new ListTag($tierList));
        $this->stream = new CacheableNbt($tag);
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

            if($nbt instanceof CompoundTag){
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
     * @return Tag[]
     */
    public function getNamedTag(): array{
        return $this->namedTag;
    }

    public function getStream(): CacheableNbt{
        return $this->stream;
    }

    public function getBuffer(): string{
        return $this->stream->getEncodedNbt();
    }
}