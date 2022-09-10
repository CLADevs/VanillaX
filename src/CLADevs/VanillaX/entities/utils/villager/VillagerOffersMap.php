<?php

namespace CLADevs\VanillaX\entities\utils\villager;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;

class VillagerOffersMap{

    const TAG_RECIPES = "Recipes";
    const TAG_TIER_EXP_REQUIREMENT = "TierExpRequirements";

    /** @var CompoundTag[] */
    private array $tierExp = [];

    /**
     * @param VillagerProfession $profession
     * @param VillagerOffer[][] $offers
     */
    public function __construct(private VillagerProfession $profession, private array $offers = []){
        for($i = 0; $i <= 4; $i++){
            $nbt = new CompoundTag();
            $nbt->setInt(strval($i), $this->profession->getProfessionExp($i));
            $this->tierExp[] = $nbt;
        }
    }

    public function getNbt(): CacheableNbt{
        $recipes = [];

        foreach($this->offers as $tier => $offers){
            foreach($offers as $offer){
                $nbt = $offer->serialize();
                $nbt->setInt(VillagerOffer::TAG_TIER, $tier);
                $recipes[] = $nbt;
            }
        }
        $root = new CompoundTag();
        $root->setTag(self::TAG_RECIPES, new ListTag($recipes));
        $root->setTag(self::TAG_TIER_EXP_REQUIREMENT, new ListTag($this->tierExp));
        return new CacheableNbt($root);
    }

    /**
     * @param int $tier
     * @param VillagerOffer|VillagerOffer[] $offers
     */
    public function addOffer(int $tier, array|VillagerOffer $offers): void{
        if(!is_array($offers)){
            $offers = [$offers];
        }
        foreach($offers as $offer){
            $this->offers[$tier][] = $offer;
        }
    }

    /**
     * @return VillagerOffer[][]
     */
    public function getOffers(): array{
        return $this->offers;
    }

    public function getProfession(): VillagerProfession{
        return $this->profession;
    }
}