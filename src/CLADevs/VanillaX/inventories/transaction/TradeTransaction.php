<?php

namespace CLADevs\VanillaX\inventories\transaction;

use CLADevs\VanillaX\entities\passive\VillagerEntity;
use CLADevs\VanillaX\entities\utils\villager\VillagerOffer;
use CLADevs\VanillaX\entities\utils\villager\VillagerTradeNBTStream;
use CLADevs\VanillaX\event\inventory\TradeItemEvent;
use CLADevs\VanillaX\inventories\actions\TradeItemAction;
use pocketmine\inventory\transaction\action\InventoryAction;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\inventory\transaction\TransactionValidationException;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\player\Player;

class TradeTransaction extends InventoryTransaction{

    private VillagerEntity $villager;

    private ?Item $input = null;
    private ?Item $input2 = null;
    private ?Item $result = null;

    public function __construct(Player $source, array $actions, VillagerEntity $villager){
        parent::__construct($source, $actions);
        $this->villager = $villager;
    }

    public function addAction(InventoryAction $action): void{
        if($action instanceof TradeItemAction){
            if($action->isInput()){
                if($this->input !== null){
                    $this->input2 = $action->getTargetItem();
                }else{
                    $this->input = $action->getTargetItem();
                }
            }else{
                $this->result = $action->getSourceItem();
            }
        }
        parent::addAction($action);
    }

    public function execute(): void{
        $nbt = $this->villager->getOffers();
        $recipes = $nbt->getValue()[VillagerTradeNBTStream::TAG_RECIPES]->getValue();

        /* @var CompoundTag $recipe */
        foreach($recipes as $recipe){
            $value = $recipe->getValue();
            $buyB = isset($value[VillagerOffer::TAG_BUY_B]) ? Item::nbtDeserialize($value[VillagerOffer::TAG_BUY_B]) : null;

            if($this->input2 !== null && $buyB !== null && !$buyB->equalsExact($this->input2)){
                continue;
            }
            $buyA = Item::nbtDeserialize($value[VillagerOffer::TAG_BUY_A]);

            if(!$buyA->equalsExact($this->input)){
                continue;
            }
            $sell = Item::nbtDeserialize($value[VillagerOffer::TAG_SELL]);
            if(!$sell->equalsExact($this->result)){
                continue;
            }
            $experience = $value[VillagerOffer::TAG_TRADER_EXP]->getValue();

            $recipe->setInt(VillagerOffer::TAG_USES, $value[VillagerOffer::TAG_USES]->getValue() + 1);

            if($experience > 0){
                $this->villager->setExperience($this->villager->getExperience() + $experience);
            }
            $nbt->setTag(VillagerTradeNBTStream::TAG_RECIPES, new ListTag($recipes));
            $this->villager->setOffers($nbt);

            $ev = new TradeItemEvent($this->source, $this->input, $this->input2, $this->result, $experience);
            $ev->call();
            parent::execute();
            return;
        }
        throw new TransactionValidationException("Invalid trade recipe");
    }

    public function canExecute(): bool{
        return $this->input !== null && $this->result !== null;
    }

    public function getInput(): ?Item{
        return $this->input;
    }

    public function getInput2(): ?Item{
        return $this->input2;
    }

    public function getResult(): ?Item{
        return $this->result;
    }

    public function getVillager(): VillagerEntity{
        return $this->villager;
    }
}