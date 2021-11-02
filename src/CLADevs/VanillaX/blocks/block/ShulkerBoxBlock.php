<?php

namespace CLADevs\VanillaX\blocks\block;

use pocketmine\block\BlockIds;
use pocketmine\block\utils\ColorBlockMetaHelper;
use pocketmine\item\ItemIds;

class ShulkerBoxBlock extends UndyedShulkerBoxBlock{

    public function __construct(int $meta = 0){
        $this->meta = $meta;
        parent::__construct(BlockIds::SHULKER_BOX, $meta, $this->getName(), ItemIds::SHULKER_BOX);
    }

    public function getName(): string{
        return ColorBlockMetaHelper::getColorFromMeta($this->getVariant()) . " Shulker Box";
    }
}