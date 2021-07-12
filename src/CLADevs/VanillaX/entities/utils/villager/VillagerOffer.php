<?php

namespace CLADevs\VanillaX\entities\utils\villager;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;

class VillagerOffer{

    const TAG_USES = "uses";
    const TAG_MAX_USES = "maxUses";
    const TAG_REWARD_EXP = "rewardExp";
    const TAG_TRADER_EXP = "traderExp";
    const TAG_PRICE_MULTIPLIER_A = "priceMultiplierA";
    const TAG_PRICE_MULTIPLIER_B = "priceMultiplierB";
    const TAG_SELL = "sell";
    const TAG_BUY_A = "buyA";
    const TAG_BUY_B = "buyB";
    const TAG_TIER = "tier";

    private ?Item $input;
    private ?Item $input2;
    private ?Item $result;
    private CompoundTag $namedtag;

    private int $uses;
    private int $maxUses;
    private bool $rewardExp;
    private int $traderExp;
    private float $priceMultiplierA;
    private float $priceMultiplierB;

    /**
     * VillagerOffer constructor.
     * @param Item|int|null $input
     * @param Item|int|null $input2
     * @param Item|int|null $result
     * @param int $traderExp
     * @param bool $rewardExp
     * @param float $priceMultiplierA
     * @param float $priceMultiplierB
     * @param int $maxUses
     * @param int $uses
     */
    public function __construct($input = null, $input2 = null, $result = null, int $traderExp = 0, bool $rewardExp = false, float $priceMultiplierA = 0, float $priceMultiplierB = 0, int $maxUses = 100, int $uses = 0){
        $this->traderExp = $traderExp;
        $this->rewardExp = $rewardExp;
        $this->priceMultiplierA = $priceMultiplierA;
        $this->priceMultiplierB = $priceMultiplierB;
        $this->maxUses = $maxUses;
        $this->uses = $uses;
        $this->setInput($input, $input2);
        $this->setResult($result);
        $this->initializeNBT();
    }

    public function initializeNBT(): void{
        $this->namedtag = new CompoundTag();
        $this->namedtag->setInt(self::TAG_USES, $this->uses);
        $this->namedtag->setInt(self::TAG_MAX_USES, $this->maxUses);
        $this->namedtag->setInt(self::TAG_TRADER_EXP, $this->traderExp);
        $this->namedtag->setFloat(self::TAG_PRICE_MULTIPLIER_A, $this->priceMultiplierA);
        $this->namedtag->setFloat(self::TAG_PRICE_MULTIPLIER_B, $this->priceMultiplierB);
        $this->namedtag->setByte(self::TAG_REWARD_EXP, $this->rewardExp);
    }

    public function asItem(): ?CompoundTag{
        $input = $this->input;
        $input2 = $this->input2;
        $result = $this->result;

        if($input !== null && $result !== null){
            $nbt = clone $this->namedtag;
            $nbt->setTag(self::TAG_SELL, $result->nbtSerialize());
            $nbt->setTag(self::TAG_BUY_A, $input->nbtSerialize());
           if($input2 !== null){
               $nbt->setTag(self::TAG_BUY_B, $input2->nbtSerialize());
           }
           return $nbt;
        }
        return null;
    }

    public function getUses(): int{
        return $this->uses;
    }

    public function setUses(int $uses): void{
        $this->uses = $uses;
        $this->initializeNBT();
    }

    public function getMaxUses(): int{
        return $this->maxUses;
    }

    public function setMaxUses(int $maxUses): void{
        $this->maxUses = $maxUses;
        $this->initializeNBT();
    }

    public function getInput(): ?Item{
        return $this->input;
    }

    public function getInput2(): ?Item{
        return $this->input2;
    }

    public function isRewardExp(): bool{
        return $this->rewardExp;
    }

    public function setRewardExp(bool $rewardExp): void{
        $this->rewardExp = $rewardExp;
    }

    /**
     * @param Item|int $input
     * @param Item|int|null $input2
     */
    public function setInput($input, $input2 = null): void{
        if(is_numeric($input)){
            $input = ItemFactory::getInstance()->get($input);
        }
        if(is_numeric($input2)){
            $input2 = ItemFactory::getInstance()->get($input2);
        }
        $this->input = $input;
        $this->input2 = $input2;
    }

    public function getResult(): ?Item{
        return $this->result;
    }

    /**
     * @param Item|int $result
     */
    public function setResult($result): void{
        if(is_numeric($result)){
            $result = ItemFactory::getInstance()->get($result);
        }
        $this->result = $result;
    }
}