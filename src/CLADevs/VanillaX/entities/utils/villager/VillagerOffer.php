<?php

namespace CLADevs\VanillaX\entities\utils\villager;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;

class VillagerOffer{

    const TAG_USES = "uses";
    const TAG_MAX_USES = "maxUses";
    const TAG_REWARD_EXP = "rewardExp";
    const TAG_PRICE_MULTIPLIER = "priceMultiplier";
    const TAG_SELL = "sell";
    const TAG_BUY_A = "buyA";
    const TAG_BUY_B = "buyB";

    private ?Item $input;
    private ?Item $input2;
    private ?Item $result;
    private CompoundTag $namedtag;

    private int $uses;
    private int $maxUses;
    private int $rewardExp;
    private int $priceMultiplier;

    /**
     * VillagerOffer constructor.
     * @param int $rewardExp
     * @param int $priceMultiplier
     * @param int $maxUses
     * @param int $uses
     * @param Item|int|null $input
     * @param Item|int|null $input2
     * @param Item|int|null $result
     */
    public function __construct(int $rewardExp = 0, int $priceMultiplier = 1, int $maxUses = 100, int $uses = 0, $input = null, $input2 = null, $result = null){
        $this->rewardExp = $rewardExp;
        $this->priceMultiplier = $priceMultiplier;
        $this->maxUses = $maxUses;
        $this->uses = $uses;
        $this->setInput($input, $input2);
        $this->setResult($result);
        $this->initializeNBT();
    }

    public function initializeNBT(): void{
        $this->namedtag = new CompoundTag("", [
            new IntTag(self::TAG_USES, $this->uses),
            new IntTag(self::TAG_MAX_USES, $this->maxUses),
            new IntTag(self::TAG_REWARD_EXP, $this->rewardExp),
            new IntTag(self::TAG_PRICE_MULTIPLIER, $this->priceMultiplier),
        ]);
    }

    public function asItem(): ?CompoundTag{
        $input = $this->input;
        $input2 = $this->input2;
        $result = $this->result;

        if($input !== null && $result !== null){
            $nbt = clone $this->namedtag;
            $nbt->setTag($result->nbtSerialize(-1, self::TAG_SELL));
            $nbt->setTag($input->nbtSerialize(-1, self::TAG_BUY_A));
           if($input2 !== null){
               $nbt->setTag($input2->nbtSerialize(-1, self::TAG_BUY_B));
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

    public function getRewardExp(): int{
        return $this->rewardExp;
    }

    public function setRewardExp(int $rewardExp): void{
        $this->rewardExp = $rewardExp;
    }

    public function getInput(): ?Item{
        return $this->input;
    }

    public function getInput2(): ?Item{
        return $this->input2;
    }

    /**
     * @param Item|int $input
     * @param Item|int|null $input2
     */
    public function setInput($input, $input2 = null): void{
        if(is_numeric($input)){
            $input = ItemFactory::get($input);
        }
        if(is_numeric($input2)){
            $input2 = ItemFactory::get($input2);
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
            $result = ItemFactory::get($result);
        }
        $this->result = $result;
    }
}