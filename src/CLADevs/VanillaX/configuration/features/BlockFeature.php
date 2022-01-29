<?php

namespace CLADevs\VanillaX\configuration\features;

use CLADevs\VanillaX\configuration\Feature;
use pocketmine\block\Block;
use pocketmine\utils\SingletonTrait;
use const pocketmine\BEDROCK_DATA_PATH;

class BlockFeature extends Feature{
    use SingletonTrait;

    /** @var string[] */
    private array $blockIdMap;
    /** @var bool[] */
    private array $blocks;

    public function __construct(){
        self::setInstance($this);
        parent::__construct("block");
        $this->blockIdMap = array_flip(json_decode(file_get_contents(BEDROCK_DATA_PATH . "/block_id_map.json"), true));
        $this->blocks = $this->config->get("blocks", []);
    }

    public function isBlockEnabled(Block $block): bool{
        $vanillaName = $this->blockIdMap[$block->getId()] ?? null;

        if($vanillaName === null){
            return false;
        }
        return $this->blocks[$vanillaName] ?? false;
    }
}