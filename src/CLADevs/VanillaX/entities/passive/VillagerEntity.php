<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\interfaces\EntityInteractable;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use CLADevs\VanillaX\entities\utils\villager\VillagerTradeNBTStream;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\inventories\types\TradeInventory;
use CLADevs\VanillaX\session\SessionManager;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\serializer\NetworkNbtSerializer;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;

class VillagerEntity extends VanillaEntity implements EntityInteractable{

    const TAG_PROFESSION = "Profession";
    const TAG_OFFER_BUFFER = "OfferBuffer";
    const TAG_TIER = "Tier";
    const TAG_EXPERIENCE = "Experience";

    const NETWORK_ID = self::LEGACY_ID_MAP_BC[self::VILLAGER_V2];

    public float $width = 0.6;
    public float $height = 1.9;

    private int $tier;
    private int $experience = 0;
    private int $biomeType = VillagerProfession::BIOME_PLAINS;

    private ?Player $customer = null;
    private TradeInventory $inventory;
    private VillagerProfession $profession;
    private CompoundTag $offers;

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

        $hasOffers = false;
        $tag = $nbt->getTag(self::TAG_OFFER_BUFFER);
        if($tag instanceof StringTag){
            /** @var CompoundTag $offers */
            $offers = (new CacheableNbt((new NetworkNbtSerializer())->read($tag->getValue())->mustGetCompoundTag()))->getRoot();
            $this->offers = $offers;
            $hasOffers = true;
        }
        $this->setProfession($this->profession, VillagerProfession::BIOME_PLAINS, $hasOffers);
        $this->setTier($this->tier);
        $this->setExperience($this->experience);
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::MAX_TRADE_TIER, VillagerProfession::TIER_MASTER);
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
            $nbt->setString(self::TAG_OFFER_BUFFER, (new CacheableNbt($this->offers))->getEncodedNbt());
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

    public function setProfession(VillagerProfession $profession, int $biomeId = VillagerProfession::BIOME_PLAINS, bool $hasBuffer = false): void{
        $this->profession = $profession;

        if(!$hasBuffer && $profession->hasTrades()){
            $stream = new VillagerTradeNBTStream($profession);
            $stream->addOffer(VillagerProfession::TIER_NOVICE, $profession->getNovice());
            $stream->addOffer(VillagerProfession::TIER_APPRENTICE, $profession->getApprentice());
            $stream->addOffer(VillagerProfession::TIER_JOURNEYMAN, $profession->getJourneyman());
            $stream->addOffer(VillagerProfession::TIER_EXPERT, $profession->getExpert());
            $stream->addOffer(VillagerProfession::TIER_MASTER, $profession->getMaster());
            $stream->initialize();
            /** @var CompoundTag $offers */
            $offers = $stream->getStream()->getRoot();
            $this->offers = $offers;
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

    public function setOffers(CompoundTag $offers): void{
        $this->offers = $offers;
    }

    public function getOffers(): CompoundTag{
        return $this->offers;
    }

    public function setCustomer(?Player $customer): void{
        $this->customer = $customer;
    }

    public function getCustomer(): ?Player{
        return $this->customer;
    }

    public function onInteract(EntityInteractResult $result): void{
        if($this->profession->hasTrades() && $this->customer === null){
            $player = $result->getPlayer();
            $this->customer = $player;
            SessionManager::getInstance()->get($player)->setTradingEntity($this);
            $player->setCurrentWindow($this->inventory);
        }
    }

    public function flagForDespawn(): void{
        parent::flagForDespawn();
        if($this->customer !== null && $this->customer->isOnline()){
            SessionManager::getInstance()->get($this->customer)->setTradingEntity(null);
        }
    }
}