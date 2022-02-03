<?php

namespace CLADevs\VanillaX\configuration\features;

use CLADevs\VanillaX\configuration\Feature;
use pocketmine\item\Item;
use pocketmine\utils\SingletonTrait;
use const pocketmine\BEDROCK_DATA_PATH;

class ItemFeature extends Feature{
    use SingletonTrait;

    /** @var string[] */
    private array $itemIdMap;
    /** @var bool[] */
    private array $items;

    public function __construct(){
        self::setInstance($this);
        parent::__construct("item");
        $this->itemIdMap = array_map(fn(string $value) => str_replace("minecraft:", "", $value), array_flip(json_decode(file_get_contents(BEDROCK_DATA_PATH . "/item_id_map.json"), true)));
        $this->items = $this->config->get("items", []);
    }

    /**
     * @return string[]
     */
    public function getItemIdMap(): array
    {
        return $this->itemIdMap;
    }

    public function isItemEnabled(Item|int $item): bool{
        $vanillaName = $this->itemIdMap[$item instanceof Item ? $item->getId() : $item] ?? null;

        if($vanillaName === null){
            return false;
        }
        return $this->items[$vanillaName] ?? true;
    }
}