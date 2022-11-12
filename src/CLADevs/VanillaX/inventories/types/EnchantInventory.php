<?php

namespace CLADevs\VanillaX\inventories\types;

use CLADevs\VanillaX\enchantments\EnchantmentManager;
use CLADevs\VanillaX\enchantments\types\SoulSpeedEnchantment;
use CLADevs\VanillaX\enchantments\VanillaEnchantment;
use CLADevs\VanillaX\event\inventory\EnchantItemEvent;
use CLADevs\VanillaX\inventories\FakeBlockInventory;
use CLADevs\VanillaX\items\LegacyItemIds;
use CLADevs\VanillaX\session\SessionManager;
use CLADevs\VanillaX\utils\Utils;
use Exception;
use pocketmine\block\Air;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Bookshelf;
use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\inventory\TemporaryInventory;
use pocketmine\item\Book;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;
use pocketmine\network\mcpe\protocol\PlayerEnchantOptionsPacket;
use pocketmine\network\mcpe\protocol\types\Enchant;
use pocketmine\network\mcpe\protocol\types\EnchantOption;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\player\Player;
use pocketmine\utils\Random;
use pocketmine\world\Position;

class EnchantInventory extends FakeBlockInventory implements TemporaryInventory, RecipeInventory{

    const SLOT_INPUT = 0;
    const SLOT_MATERIAL = 1;

    /** @var EnchantOption[] */
    private array $options = [];

    public function __construct(Position $holder){
        parent::__construct($holder, 2, BlockLegacyIds::AIR, WindowTypes::ENCHANTMENT);
    }

    protected function onSlotChange(int $index, Item $before): void{
        if($index === self::SLOT_INPUT){
            foreach($this->viewers as $viewer){
                $this->sendEnchantmentOptions($this->getItem($index), $viewer);
            }
        }
        parent::onSlotChange($index, $before);
    }

    public function sendEnchantmentOptions(Item $input, Player $player): void{
        $session = SessionManager::getInstance()->get($player);
        $random = new Random($session->getXpSeed());
        $options = [];

        if(!$input->isNull() && !$input->hasEnchantments()){
            $bookshelfCount = $this->countBookshelves();
            $baseCost = ($random->nextBoundedInt(8) + 1) + floor($bookshelfCount >> 1) + $random->nextBoundedInt($bookshelfCount + 1);
            $topCost = floor(max($baseCost / 3, 1));
            $middleCost = floor($baseCost * 2 / 3 + 1);
            $bottomCost = floor(max($baseCost, $bookshelfCount * 2));

            $options = [
                $this->createOption($random, $input, $topCost),
                $this->createOption($random, $input, $middleCost),
                $this->createOption($random, $input, $bottomCost),
            ];
        }
        $player->getNetworkSession()->sendDataPacket(PlayerEnchantOptionsPacket::create($options));
    }

    public function createOption(Random $random, Item $input, int $optionCost): EnchantOption{
        $cost = $optionCost;
        $ability = $this->getEnchantability($input);
        $enchantAbility = $cost + 1 + $random->nextBoundedInt($ability / 4 + 1) + $random->nextBoundedInt($ability / 4 + 1);
        $enchantAbility = Utils::clamp(round($cost + $cost * $enchantAbility), 1, PHP_INT_MAX);
        $enchantments = $this->getAvailableEnchantments($cost, $input);
        /** @var EnchantmentInstance[] $list */
        $list = [];

        if(count($enchantments) >= 1){
            $weightedEnchantment = $this->getRandomWeightedEnchantment($random, $enchantments);

            if($weightedEnchantment !== null){
                $list[] = $weightedEnchantment;
            }

            while($random->nextBoundedInt(50) <= $enchantAbility){
                if(count($list) >= 1){
                    $enchantments = $this->filterEnchantments($enchantments, $list[array_key_last($list)]);
                }
                if(count($enchantments) < 1){
                    break;
                }
                $weightedEnchantment = $this->getRandomWeightedEnchantment($random, $enchantments);

                if($weightedEnchantment !== null){
                    $list[] = $weightedEnchantment;
                }
                $enchantAbility /= 2;
            }
        }

        $enchants = [];
        foreach($list as $enchantment){
            $type = $enchantment->getType();

            if($type instanceof VanillaEnchantment){
                $enchants[] = new Enchant($type->getMcpeId(), $enchantment->getLevel());
            }
        }
        $slot = count($this->options);
        $this->options[] = new EnchantOption($optionCost, $slot, $enchants, [], [], "OMG TESTING", $slot);
        return $this->options[$slot];
    }

    public function getResultItem(Player $player, int $netId): ?Item{
        $option = $this->options[$netId] ?? null;

        if($option === null){
            throw new Exception("Failed to find enchantment option for network id: $netId");
        }
        $this->options = [];
        $enchantments = array_merge($option->getEquipActivatedEnchantments(), $option->getHeldActivatedEnchantments(), $option->getSelfActivatedEnchantments());
        foreach($enchantments as $index => $enchant){
            $enchantments[$index] = new EnchantmentInstance(EnchantmentIdMap::getInstance()->fromId($enchant->getId()), $enchant->getLevel());
        }
        $result = $this->getItem(self::SLOT_INPUT);
        $cost = $option->getCost();
        $expectedMaterial = $netId + 1;

        $ev = new EnchantItemEvent($player, $result, $enchantments, $cost, $expectedMaterial);
        $ev->call();
        if($ev->isCancelled()){
            return $result; //should return the item player was trying to enchant with no enchantments
        }
        $result = $ev->getInput();
        $cost = $ev->getLevelCost();
        $expectedMaterial = $ev->getMaterialsCost();
        $enchantments = $ev->getEnchantments();

        if(!$player->isCreative()){
            $xpManager = $player->getXpManager();
            $currentXp = $xpManager->getXpLevel();
            $material = $this->getItem(self::SLOT_MATERIAL);
            $currentMaterial = $material->getCount();

            if($currentXp < $cost){
                throw new Exception("Expected player to have xp level of $cost, but received $currentXp");
            }
            if($material->getId() !== ItemIds::DYE && $material->getMeta() !== 4){
                throw new Exception("Invalid material item");
            }
            if($currentMaterial < $expectedMaterial){
                throw new Exception("Expected material count to be $expectedMaterial, but received $currentMaterial");
            }
            $xpManager->subtractXpLevels($cost);
        }
        foreach($enchantments as $enchantment){
            $result->addEnchantment($enchantment);
        }
        return $result;
    }

    /**
     * @param int $cost
     * @param Item $input
     * @return EnchantmentInstance[]
     */
    public function getAvailableEnchantments(int $cost, Item $input): array{
        $list = [];

        foreach(EnchantmentManager::getInstance()->getAllEnchantments(false) as $enchantment){
            if(!$enchantment instanceof SoulSpeedEnchantment){
                if($input instanceof Book || $enchantment->isItemCompatible($input)){
                    for($i = $enchantment->getMaxLevel(); $i > 0; $i--){
                        if($cost >= $enchantment->getMinCost($i) && $cost <= $enchantment->getMaxCost($i)){
                            if($enchantment instanceof Enchantment){
                                $list[] = new EnchantmentInstance($enchantment, $i);
                                break;
                            }
                        }
                    }
                }
            }
        }
        return $list;
    }

    /**
     * @param EnchantmentInstance[] $enchantments
     * @param EnchantmentInstance $last
     * @return EnchantmentInstance[]
     */
    public function filterEnchantments(array $enchantments, EnchantmentInstance $last): array{
        foreach($enchantments as $index => $enchantment){
            $filterType = $enchantment->getType();
            $type = $last->getType();

            if($type instanceof VanillaEnchantment && $filterType instanceof VanillaEnchantment){
                if($type->isIncompatibleWith($filterType)){
                    unset($enchantments[$index]);
                }
            }
        }
        return $enchantments;
    }

    /**
     * @param Random $random
     * @param EnchantmentInstance[] $enchantments
     * @return EnchantmentInstance|null
     */
    public function getRandomWeightedEnchantment(Random $random, array $enchantments): ?EnchantmentInstance{
        $totalWeight = 0;

        foreach($enchantments as $enchantment){
            $totalWeight += $enchantment->getType()->getRarity();
        }
        $i = $random->nextBoundedInt($totalWeight);

        foreach($enchantments as $enchantment){
            $i -= $enchantment->getType()->getRarity();

            if($i < 0){
                return $enchantment;
            }
        }
        return null;
    }

    public function countBookshelves(): int{
        $shelves = 0;
        $pos = $this->holder;
        $world = $pos->getWorld();

        for ($x = -1; $x <= 1; $x++){
            for ($z = -1; $z <= 1; $z++){
                if($z !== 0 || $x !== 0){
                    for($y = 0; $y <= 1; $y++){
                        $block = $world->getBlock((clone $pos)->add($x, $y, $z));

                        if(($x !== 0 || $z !== 0) && $block instanceof Air){
                            $block = $world->getBlock((clone $pos)->add($x * 2, $y, $z * 2));

                            if($block instanceof Bookshelf){
                                $shelves++;
                            }

                            if($x !== 0 && $z !== 0){
                                $block = $world->getBlock((clone $pos)->add($x * 2, $y, $z));

                                if($block instanceof Bookshelf){
                                    $shelves++;
                                }
                                $block = $world->getBlock((clone $pos)->add($x, $y, $z * 2));

                                if($block instanceof Bookshelf){
                                    $shelves++;
                                }
                            }
                        }
                        if($shelves >= 15){
                            return 15;
                        }
                    }
                }
            }
        }
        return $shelves;
    }

    public function getEnchantability(Item $input): int{
        if($input instanceof ToolTier){
            switch($input->name()){
                case "wood":
                    return 15;
                case "stone":
                    return 5;
                case "iron":
                    return 14;
                case "gold":
                    return 22;
                case "diamond":
                    return 10;
            }
        }
        return match ($input->getId()) {
            ItemIds::LEATHER_CAP, ItemIds::LEATHER_CHESTPLATE, ItemIds::LEATHER_LEGGINGS, ItemIds::LEATHER_BOOTS => 15,
            ItemIds::CHAIN_HELMET, ItemIds::CHAIN_CHESTPLATE, ItemIds::CHAIN_LEGGINGS, ItemIds::CHAIN_BOOTS => 12,
            ItemIds::IRON_HELMET, ItemIds::IRON_CHESTPLATE, ItemIds::IRON_LEGGINGS, ItemIds::IRON_BOOTS, ItemIds::TURTLE_SHELL_PIECE => 9,
            ItemIds::GOLD_HELMET, ItemIds::GOLD_CHESTPLATE, ItemIds::GOLD_LEGGINGS, ItemIds::GOLD_BOOTS => 25,
            ItemIds::DIAMOND_HELMET, ItemIds::DIAMOND_CHESTPLATE, ItemIds::DIAMOND_LEGGINGS, ItemIds::DIAMOND_BOOTS => 10,
            LegacyItemIds::NETHERITE_HELMET, LegacyItemIds::NETHERITE_CHESTPLATE, LegacyItemIds::NETHERITE_LEGGINGS, LegacyItemIds::NETHERITE_BOOTS, LegacyItemIds::NETHERITE_AXE, LegacyItemIds::NETHERITE_PICKAXE, LegacyItemIds::NETHERITE_HOE, LegacyItemIds::NETHERITE_SHOVEL, LegacyItemIds::NETHERITE_SWORD => 15,
            default => 1,
        };
    }
}