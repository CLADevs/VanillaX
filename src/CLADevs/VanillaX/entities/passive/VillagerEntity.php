<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\villager\VillagerOffer;
use CLADevs\VanillaX\entities\utils\villager\VillagerOffersMap;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\inventories\types\TradeInventory;
use CLADevs\VanillaX\session\SessionManager;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;

class VillagerEntity extends VanillaEntity{

    const TAG_PROFESSION = "Profession";
    const TAG_OFFER_BUFFER = "OfferBuffer";
    const TAG_TIER = "Tier";
    const TAG_EXPERIENCE = "Experience";

    const NETWORK_ID = EntityIds::VILLAGER_V2;

    public float $width = 0.6;
    public float $height = 1.9;

    private int $tier;
    private int $experience = 0;
    private int $biomeType = VillagerProfession::BIOME_PLAINS;

    private ?Player $customer = null;
    private TradeInventory $inventory;
    private VillagerProfession $profession;
    private VillagerOffersMap $offers;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(20);
        $this->inventory = new TradeInventory($this);
        /** Tier */
        $tag = $nbt->getTag(self::TAG_TIER);
        if($tag instanceof IntTag){
            $this->tier = $tag->getValue();
        }else{
            $this->tier = VillagerProfession::TIER_NOVICE;
        }
        /** Experience */
        $tag = $nbt->getTag(self::TAG_TIER);
        if($tag instanceof IntTag){
            $this->experience = $tag->getValue();
        }
        /** Profession */
        $tag = $nbt->getTag(self::TAG_PROFESSION);
        if($tag instanceof IntTag){
            $profession = $tag->getValue();
        }else{
            $profession = mt_rand(VillagerProfession::UNEMPLOYED, VillagerProfession::NITWIT);
        }
        $this->profession = VillagerProfession::getProfession($profession);

        $resetTrades = true;
        $tag = $nbt->getTag(self::TAG_OFFER_BUFFER);
        if($tag instanceof StringTag){
            $offers = [];

            /** @var CompoundTag $nbt */
            foreach((new CacheableNbt((new NetworkNbtSerializer())->read($tag->getValue())->mustGetCompoundTag()))->getRoot()->getValue()[VillagerOffersMap::TAG_RECIPES]->getValue() as $nbt){
                $tier = $nbt->getInt(VillagerOffer::TAG_TIER);
                $offers[$tier][] = VillagerOffer::deserialize($nbt);
                $this->offers = new VillagerOffersMap($this->profession, $offers);
                $resetTrades = false;
            }
        }
        $this->setProfession($this->profession, VillagerProfession::BIOME_PLAINS, $resetTrades);
        $this->setTier($this->tier);
        $this->setExperience($this->experience);
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::MAX_TRADE_TIER, VillagerProfession::TIER_MASTER);;
    }

    public function getName(): string{
        return "Villager";
    }

    public function saveNBT(): CompoundTag{
        $nbt = parent::saveNBT();
        $nbt->setInt(self::TAG_PROFESSION, $this->profession->getId());
        if($this->profession->hasTrades()){
            $nbt->setInt(self::TAG_TIER, $this->tier);
            $nbt->setInt(self::TAG_EXPERIENCE, $this->experience);
            $nbt->setString(self::TAG_OFFER_BUFFER, $this->offers->getNbt()->getEncodedNbt());
        }
        return $nbt;
    }

    public function setTier(int $tier): void{
        if($tier < VillagerProfession::TIER_NOVICE){
            $tier = VillagerProfession::TIER_NOVICE;
        }elseif($tier > VillagerProfession::TIER_MASTER){
            $tier = VillagerProfession::TIER_MASTER;
        }
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::TRADE_TIER, $tier);
        $this->tier = $tier;
    }

    public function getTier(): int{
        return $this->tier;
    }

    public function setExperience(int $experience): void{
        if($this->tier >= VillagerProfession::TIER_MASTER){
            return;
        }
        $exp = $this->profession->getProfessionExp($this->tier + 1);

        if($exp === 0){
            return;
        }
        if($experience > $exp){
            $experience = 0;
            $this->setTier($this->tier + 1);
        }
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::TRADE_XP, $experience);
        $this->experience = $experience;
    }

    public function getExperience(): int{
        return $this->experience;
    }

    public function setProfession(VillagerProfession $profession, int $biomeId = VillagerProfession::BIOME_PLAINS, bool $resetTrades = true): void{
        $this->profession = $profession;

        if($resetTrades && $profession->hasTrades()){
            $this->offers = new VillagerOffersMap($profession);
            $this->offers->addOffer(VillagerProfession::TIER_NOVICE, $profession->getNovice());
            $this->offers->addOffer(VillagerProfession::TIER_APPRENTICE, $profession->getApprentice());
            $this->offers->addOffer(VillagerProfession::TIER_JOURNEYMAN, $profession->getJourneyman());
            $this->offers->addOffer(VillagerProfession::TIER_EXPERT, $profession->getExpert());
            $this->offers->addOffer(VillagerProfession::TIER_MASTER, $profession->getMaster());
        }
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $profession->getId());
        $this->setBiomeType($biomeId);
    }

    public function getProfession(): VillagerProfession{
        return $this->profession;
    }

    public function setBiomeType(int $type): void{
        if($type < VillagerProfession::BIOME_PLAINS || $type > VillagerProfession::BIOME_TAIGA){
            $type = VillagerProfession::BIOME_PLAINS;
        }
        $this->biomeType = $type;
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::MARK_VARIANT, $type);
    }

    public function getBiomeType(): int{
        return $this->biomeType;
    }

    public function getOffers(): VillagerOffersMap{
        return $this->offers;
    }

    public function setCustomer(?Player $customer): void{
        $this->customer = $customer;
    }

    public function getCustomer(): ?Player{
        return $this->customer;
    }

    public function onInteract(Player $player, Vector3 $clickPos): bool{
        if($this->profession->hasTrades() && $this->customer === null){
            $this->customer = $player;
            SessionManager::getInstance()->get($player)->setTradingEntity($this);
            $player->setCurrentWindow($this->inventory);
            return true;
        }
        return false;
    }

    public function flagForDespawn(): void{
        parent::flagForDespawn();
        if($this->customer !== null && $this->customer->isOnline()){
            SessionManager::getInstance()->get($this->customer)->setTradingEntity(null);
        }
    }
}