<?php

namespace CLADevs\VanillaX\entities\loot;

use CLADevs\VanillaX\entities\loot\functions\EnchantRandomGearFunction;
use CLADevs\VanillaX\entities\loot\functions\EnchantRandomlyFunction;
use CLADevs\VanillaX\entities\loot\functions\EnchantWithLevelsFunction;
use CLADevs\VanillaX\entities\loot\functions\ExplorationMapFunction;
use CLADevs\VanillaX\entities\loot\functions\FurnaceSmeltFunction;
use CLADevs\VanillaX\entities\loot\functions\LootingEnchantFunction;
use CLADevs\VanillaX\entities\loot\functions\RandomAuxValueFunction;
use CLADevs\VanillaX\entities\loot\functions\SetBannerDetailsFunction;
use CLADevs\VanillaX\entities\loot\functions\SetCountFunction;
use CLADevs\VanillaX\entities\loot\functions\SetDamageFunction;
use CLADevs\VanillaX\entities\loot\functions\SetDataFromColorIndexFunction;
use CLADevs\VanillaX\entities\loot\functions\SetDataFunction;
use CLADevs\VanillaX\entities\loot\functions\SpecificEnchantsFunction;
use CLADevs\VanillaX\VanillaX;
use InvalidArgumentException;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class LootEntry{

    public static array $unknownItems = [];

    const TYPE_ITEM = "item";
    const TYPE_EMPTY = "empty";

    private array $entry;

    /** @var LootFunction[] */
    private array $functions = [];

    private string $type;
    private Item $item;
    private int $weight;

    public function __construct(array $entry){
        $this->entry = $entry;
        $this->type = $entry["type"] ?? self::TYPE_ITEM;
        $this->weight = $entry["weight"] ?? 1;

        if($this->type === self::TYPE_ITEM){
            try{
                $this->item = ItemFactory::fromStringSingle($entry["name"]);
            }catch(InvalidArgumentException $e){
             //TODO   VanillaX::getInstance()->getLogger()->error("Could not find item " . $entry["name"] . " putting air.");
                $this->item = ItemFactory::get(ItemIds::AIR);
            }
        }else{
            $this->item = ItemFactory::get(ItemIds::AIR);
        }
        foreach(($entry["functions"] ?? []) as $fi){
            switch($function = str_replace("minecraft:", "", $fi["function"])){
                case EnchantRandomlyFunction::NAME:
                    $this->functions[] = new EnchantRandomlyFunction($fi["treasure"] ?? false);
                    break;
                case SetCountFunction::NAME:
                    $min = $fi["count"]["min"] ?? $fi["count"] ?? 0;
                    $max = $fi["count"]["max"] ?? $fi["count"] ?? 0;
                    $this->functions[] = new SetCountFunction($min, $max);
                    break;
                case SetDataFunction::NAME:
                    $this->functions[] = new SetDataFunction($fi["data"]);
                    break;
                case SetDamageFunction::NAME:
                    $min = $fi["damage"]["min"] ?? $fi["damage"] ?? 0;
                    $max = $fi["damage"]["max"] ?? $fi["damage"] ?? 0;
                    $this->functions[] = new SetDamageFunction($min, $max);
                    break;
                case SpecificEnchantsFunction::NAME:
                    $this->functions[] = new SpecificEnchantsFunction($fi["enchants"]);
                    break;
                case EnchantWithLevelsFunction::NAME:
                    $min = $fi["level"]["min"] ?? $fi["level"] ?? 0;
                    $max = $fi["level"]["max"] ?? $fi["level"] ?? 0;
                    $this->functions[] = new EnchantWithLevelsFunction($min, $max, $fi["treasure"] ?? false);
                    break;
                case ExplorationMapFunction::NAME:
                    $this->functions[] = new ExplorationMapFunction($fi["destination"]);
                    break;
                case RandomAuxValueFunction::NAME:
                    $this->functions[] = new RandomAuxValueFunction($fi["values"]["min"], $fi["values"]["max"]);
                    break;
                case EnchantRandomGearFunction::NAME:
                    $this->functions[] = new EnchantRandomGearFunction($fi["chance"]);
                    break;
                case LootingEnchantFunction::NAME:
                    $this->functions[] = new LootingEnchantFunction();
                    break;
                case SetDataFromColorIndexFunction::NAME:
                    $this->functions[] = new SetDataFromColorIndexFunction();
                    break;
                case FurnaceSmeltFunction::NAME:
                    $this->functions[] = new FurnaceSmeltFunction();
                    break;
                case SetBannerDetailsFunction::NAME:
                    $this->functions[] = new SetBannerDetailsFunction($fi["type"]);
                    break;
                default:
                    VanillaX::getInstance()->getLogger()->error("Unknown function: " . $function);
                    break;
            }
        }
    }

    public function apply(?Entity $killer = null): Item{
        $item = clone $this->item;
        foreach($this->functions as $function){
            $function->apply($item);
            if($killer !== null){
                $function->customApply($killer, $item);
            }

            if($item->getCount() > $item->getMaxStackSize()){
                $item->setCount($item->getMaxStackSize());
            }
        }
        return $item;
    }
}