<?php

namespace CLADevs\VanillaX\entities\utils\villager;

use CLADevs\VanillaX\entities\utils\ItemHelper;
use CLADevs\VanillaX\entities\utils\villager\professions\NitwitProfession;
use CLADevs\VanillaX\utils\Utils;
use InvalidArgumentException;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;

abstract class VillagerProfession{

    const TIER_NOVICE = 0;
    const TIER_APPRENTICE = 1;
    const TIER_JOURNEYMAN = 2;
    const TIER_EXPERT = 3;
    const TIER_MASTER = 4;

    const BIOME_PLAINS = 0;
    const BIOME_DESERT = 1;
    const BIOME_JUNGLE = 2;
    const BIOME_SAVANNA = 3;
    const BIOME_SNOW = 4;
    const BIOME_SWAMP = 5;
    const BIOME_TAIGA = 6;

    const UNEMPLOYED = 0;
    const FARMER = 1;
    const FISHERMAN = 2;
    const SHEPHERD = 3;
    const FLETCHER = 4;
    const LIBRARIAN = 5;
    const CARTOGRAPHER = 6;
    const CLERIC = 7;
    const ARMORER = 8;
    const WEAPON_SMITH = 9;
    const TOOL_SMITH = 10;
    const BUTCHER = 11;
    const LEATHER_WORKER = 12;
    const MASON = 14;
    const NITWIT = 15;

    private int $id;
    private string $name;
    private int $block;

    /** @var string[] */
    protected array $data = [];

    /** @var VillagerProfession[] */
    private static array $professions = [];

    /**
     * @param int $id
     * @param string $name
     * @param Block|int $block
     */
    public function __construct(int $id, string $name, $block = BlockLegacyIds::AIR){
        $this->id = $id;
        $this->name = $name;
        //TODO uncomment this on 4.0
//        if(is_int($block)){
//            try{
//                $block = BlockFactory::get($block);
//            }catch (Exception $e){
//                $block = BlockFactory::get(BlockLegacyIds::AIR);
//            }
//        }
        $this->block = $block;
        if($this->hasTrades()){
            $this->data = json_decode(file_get_contents(Utils::getResourceFile("trades" . DIRECTORY_SEPARATOR . strtolower(str_replace(" ", "_", $name)) . "_trades.json")), true);
        }
    }

    public static function init(): void{
        self::$professions = [];
        $path = "entities" . DIRECTORY_SEPARATOR . "utils" . DIRECTORY_SEPARATOR . "villager" . DIRECTORY_SEPARATOR . "professions";
        Utils::callDirectory($path, function (string $namespace): void{
            self::registerProfession(new $namespace());
        });
    }

    public static function registerProfession(VillagerProfession $profession): void{
        self::$professions[$profession->getId()] = $profession;
    }

    public static function getProfession(int $id): VillagerProfession{
        return self::$professions[$id] ?? new NitwitProfession();
    }

    public function getId(): int{
        return $this->id;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getBlock(): int{
        return $this->block;
    }

    public function hasTrades(): bool{
        return true;
    }

    public function getProfessionExp(int $tier): int{
        return $this->data["tiers"][$tier]["total_exp_required"] ?? 0;
    }

    public function getNovice(): array{
        return $this->getTrades(self::TIER_NOVICE);
    }

    /**
     * @return VillagerOffer[]
     */
    public function getApprentice(): array{
        return $this->getTrades(self::TIER_APPRENTICE);
    }

    /**
     * @return VillagerOffer[]
     */
    public function getJourneyman(): array{
        return $this->getTrades(self::TIER_JOURNEYMAN);
    }

    /**
     * @return VillagerOffer[]
     */
    public function getExpert(): array{
        return $this->getTrades(self::TIER_EXPERT);
    }

    /**
     * @return VillagerOffer[]
     */
    public function getMaster(): array{
        return $this->getTrades(self::TIER_MASTER);
    }

    /**
     * @param int $tier
     * @return VillagerOffer[]
     */
    private function getTrades(int $tier): array{
        $values = [];
        $data = $this->data["tiers"][$tier]["groups"] ?? [];

        if(count($data) < 1){
            return [];
        }
        foreach($data as $i){
            $trades = $i["trades"];

            if(count($trades) > 1){
                $trades = [$trades[array_rand($trades)]];
            }
            foreach($trades as $trade){
                $wants = $trade["wants"];
                $gives = $trade["gives"];
                $traderExp = $trade["trader_exp"];
                $rewardExp = $trade["reward_exp"];
                $maxUses = $trade["max_uses"];
                $priceMultiplierA = 1;
                $priceMultiplierB = 1;
                $input = null;
                $input2 = null;
                $result = null;

                foreach($wants as $key => $want){
                    $item = $want["item"] ?? null;
                    $functions = $give["functions"] ?? [];

                    try{
                        $choice = $want["choice"] ?? null;

                        if($item === null && $choice === null){
                            continue;
                        }
                        if(is_array($choice)){
                            $item = $choice[array_rand($choice)];
                            $functions = $item["functions"] ?? [];
                        }
                        $item = LegacyStringToItemParser::getInstance()->parse(is_array($item) ? $item["item"] : $item);
                        $item->setCount(is_array($item) ? ($item["quantity"] ?? 1) : ($want["quantity"] ?? 1));
                        $this->applyFunction($item, $functions);
                    }catch (InvalidArgumentException $e){
                        continue;
                    }

                    if($key === 0){
                        $input = $item;
                        $priceMultiplierA = $want["price_multiplier"] ?? 1;
                    }elseif($key === 1){
                        $input2 = $item;
                        $priceMultiplierB = $want["price_multiplier"] ?? 1;
                        break;
                    }
                }
                foreach($gives as $give){
                    $item = $give["item"] ?? null;
                    $functions = $give["functions"] ?? [];

                    try{
                        $choice = $give["choice"] ?? null;

                        if($item === null && $choice === null){
                            continue;
                        }
                        if(is_array($choice)){
                            $item = $choice[array_rand($choice)];
                            $functions = $item["functions"] ?? [];
                        }
                        $item = LegacyStringToItemParser::getInstance()->parse(is_array($item) ? $item["item"] : $item);
                        $item->setCount(is_array($item) ? ($item["quantity"] ?? 1) : ($give["quantity"] ?? 1));
                        $this->applyFunction($item, $functions);
                    }catch (InvalidArgumentException $e){
                        continue;
                    }
                    $result = $item;
                    break;
                }
                $values[] = new VillagerOffer($input, $input2, $result, $traderExp, $rewardExp, $priceMultiplierA, $priceMultiplierB, $maxUses);
            }
        }
        return $values;
    }

    private function applyFunction(Item $item, array $functions): void{
        foreach($functions as $function){
            $name = $function["function"] ?? null;

            switch($name){
                case "enchant_with_levels":
                    ItemHelper::applyEnchantWithLevel($item, $function["treasure"] ?? false, $function["levels"]["min"], $function["levels"]["max"]);
                    return;
                case "exploration_map":
                    //TODO
                    return;
                case "random_aux_value":
                    ItemHelper::applyRandomAuxValue($item, $function["values"]["min"], $function["values"]["max"]);
                    return;
                case "random_dye":
                    ItemHelper::applyRandomDye($item);
                    return;
                case "enchant_book_for_trading":
                    //TODO for now using random enchants
                    ItemHelper::applyEnchantRandomly($item, true);
                    return;
                case "random_block_state":
                    ItemHelper::applyRandomAuxValue($item, 0, 15);
                    return;
            }
        }
    }
}