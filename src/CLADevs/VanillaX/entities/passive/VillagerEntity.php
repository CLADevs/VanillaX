<?php

namespace CLADevs\VanillaX\entities\passive;

use CLADevs\VanillaX\entities\utils\EntityInteractResult;
use CLADevs\VanillaX\entities\utils\interfaces\EntityInteractable;
use CLADevs\VanillaX\entities\utils\villager\VillagerProfession;
use CLADevs\VanillaX\entities\utils\villager\VillagerTradeNBTStream;
use CLADevs\VanillaX\entities\VanillaEntity;
use CLADevs\VanillaX\inventories\types\TradeInventory;
use CLADevs\VanillaX\VanillaX;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\network\mcpe\protocol\types\entity\IntMetadataProperty;
use pocketmine\player\Player;

class VillagerEntity extends VanillaEntity implements EntityInteractable{

    const TAG_PROFESSION = "Profession";
    const TAG_OFFER_BUFFER = "OfferBuffer";
    const TAG_TIER = "Tier";
    const TAG_EXPERIENCE = "Experience";

    const NETWORK_ID = self::VILLAGER_V2;

    public $width = 0.6;
    public $height = 1.9;

    private ?Player $customer = null;
    private TradeInventory $inventory;
    private VillagerProfession $profession;

    private int $tier;
    private int $experience = 0;
    private string $offerBuffer;

    protected function initEntity(CompoundTag $nbt): void{
        parent::initEntity($nbt);
        $this->setMaxHealth(20);
        $this->inventory = new TradeInventory($this);
        /** Tier */
        if(($tag = $nbt->getTag(self::TAG_TIER)) !== null){
            $this->tier = $nbt->getInt(self::TAG_TIER);
        }else{
            $this->tier = VillagerProfession::TIER_NOVICE;
        }
        /** Experience */
        if(($tag = $nbt->getTag(self::TAG_EXPERIENCE)) !== null){
            $this->experience = $nbt->getInt(self::TAG_EXPERIENCE);
        }
        /** Profession */
        if(($tag = $nbt->getTag(self::TAG_PROFESSION)) !== null){
            $profession = $nbt->getInt(self::TAG_PROFESSION);
        }else{
            $profession = mt_rand(VillagerProfession::UNEMPLOYED, VillagerProfession::NITWIT);
        }
        $this->profession = VillagerProfession::getProfession($profession);

        if($hasOfferBuffer = ($tag = $nbt->getTag(self::TAG_OFFER_BUFFER)) !== null){
            $this->offerBuffer = $nbt->getString(self::TAG_OFFER_BUFFER);
        }
        $this->setProfession($this->profession, VillagerProfession::BIOME_PLAINS, $hasOfferBuffer);
        $this->setTier($this->tier);
        $this->setExperience($this->experience);
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::MAX_TRADE_TIER, VillagerProfession::TIER_MASTER);
    }

    public function getName(): string{
        return "Villager";
    }

    protected function getInitialSizeInfo(): EntitySizeInfo{
        return new EntitySizeInfo($this->height, $this->width);
    }

    public static function getNetworkTypeId(): string{
        return self::NETWORK_ID;
    }

    public function saveNBT(): CompoundTag{
        $nbt = parent::saveNBT();
        $nbt->setInt(self::TAG_PROFESSION, $this->profession->getId());
        if($this->profession->hasTrades()){
            $nbt->setInt(self::TAG_TIER, $this->tier);
            $nbt->setInt(self::TAG_EXPERIENCE, $this->experience);
            $nbt->setString(self::TAG_OFFER_BUFFER, $this->offerBuffer);
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
            $this->offerBuffer = $stream->getBuffer();
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
        $this->getNetworkProperties()->setInt(EntityMetadataProperties::MARK_VARIANT, $type);
    }

    public function getBiomeType(): int{
        /** @var IntMetadataProperty $data */
        $data = $this->getNetworkProperties()->getAll()[EntityMetadataProperties::MARK_VARIANT] ?? null;
        if($data !== null){
            $data = $data->getValue();
        }
        return $data ?? VillagerProfession::BIOME_PLAINS;
    }

    public function setOfferBuffer(string $offerBuffer): void{
        $this->offerBuffer = $offerBuffer;
    }

    public function getOfferBuffer(): string{
        return $this->offerBuffer;
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
            VanillaX::getInstance()->getSessionManager()->get($player)->setTradingEntity($this);
            $player->setCurrentWindow($this->inventory);
        }
    }

    public function flagForDespawn(): void{
        parent::flagForDespawn();
        if($this->customer !== null && $this->customer->isOnline()){
            VanillaX::getInstance()->getSessionManager()->get($this->customer)->setTradingEntity(null);
        }
    }
}