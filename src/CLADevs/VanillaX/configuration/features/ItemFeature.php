<?php

namespace CLADevs\VanillaX\configuration\features;

use CLADevs\VanillaX\configuration\Feature;
use CLADevs\VanillaX\utils\Utils;
use pocketmine\item\Item;
use pocketmine\utils\SingletonTrait;

class ItemFeature extends Feature{
    use SingletonTrait;

    /** @var string[] */
    private array $itemIdMap;
    /** @var bool[] */
    private array $items;

    public function __construct(){
        self::setInstance($this);
        parent::__construct("item");
        $this->itemIdMap = array_map(fn(string $value) => str_replace("minecraft:", "", $value), array_flip(Utils::getItemIdsMap()));
        $this->items = $this->config->get("items", []);
    }

    public function isItemEnabled(Item|int $item): bool{
        $vanillaName = $this->getVanillaName($item);

        if($vanillaName === null){
            return false;
        }
        return $this->items[$vanillaName] ?? true;
    }

    public function getVanillaName(Item|int $item): ?string{
        return $this->itemIdMap[$item instanceof Item ? $item->getId() : $item] ?? null;
    }
}