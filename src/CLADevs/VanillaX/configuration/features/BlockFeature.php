<?php

namespace CLADevs\VanillaX\configuration\features;

use CLADevs\VanillaX\configuration\Feature;
use CLADevs\VanillaX\utils\Utils;
use pocketmine\block\Block;
use pocketmine\utils\SingletonTrait;

class BlockFeature extends Feature{
    use SingletonTrait;

    /** @var string[] */
    private array $blockIdMap;
    /** @var bool[] */
    private array $blocks;

    public function __construct(){
        self::setInstance($this);
        parent::__construct("block");
        $this->blockIdMap = array_map(fn(string $value) => str_replace("minecraft:", "", $value), array_flip(Utils::getBlockIdsMap()));
        $this->blocks = $this->config->get("blocks", []);
    }

    public function isBlockEnabled(Block|int $block): bool{
        $vanillaName = $this->blockIdMap[$block instanceof Block ? $block->getId() : $block] ?? null;

        if($vanillaName === null){
            return false;
        }
        return $this->blocks[$vanillaName] ?? true;
    }
}