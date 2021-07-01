<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\blocks\tile\ShulkerBoxTile;
use CLADevs\VanillaX\blocks\utils\TileUtils;
use CLADevs\VanillaX\blocks\utils\TileVanilla;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\Block;
use pocketmine\block\Transparent;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;
use pocketmine\tile\Container;

class ShulkerBoxBlock extends Transparent implements NonAutomaticCallItemTrait{
    
    public function getHardness(): float{
        return 2;
    }

    public function getBlastResistance(): float{
        return 6;
    }

    public function getDrops(Item $item): array{
        $drops = parent::getDrops($item);
        $tile = $this->getLevel()->getTile($this);

        if($tile instanceof ShulkerBoxTile){
            foreach($drops as $drop){
                $items = [];
                foreach($tile->getRealInventory()->getContents() as $slot => $item){
                    $items[] = $item->nbtSerialize($slot);
                }
                if(count($items) >= 1){
                    $drop->setNamedTagEntry(new ListTag(Container::TAG_ITEMS, $items, NBT::TAG_Compound));
                    return [$drop];
                }
            }
        }
        return $drops;
    }

    public function place(Item $item, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector, Player $player = null): bool{
        parent::place($item, $blockReplace, $blockClicked, $face, $clickVector, $player);
        $tile = TileUtils::generateTile($this, TileVanilla::SHULKER_BOX, ShulkerBoxTile::class, [new ByteTag(ShulkerBoxTile::TAG_FACING, $face)]);

        if($tile instanceof ShulkerBoxTile){
            $tag = $item->getNamedTag();

            if($tag->hasTag(Container::TAG_ITEMS, ListTag::class)){
                $inventoryTag = $tag->getListTag(Container::TAG_ITEMS);
                $inventory = $tile->getRealInventory();

                /** @var CompoundTag $itemNBT */
                foreach($inventoryTag as $itemNBT){
                    $inventory->setItem($itemNBT->getByte("Slot"), Item::nbtDeserialize($itemNBT));
                }
            }
        }
        return true;
    }

    public function onActivate(Item $item, Player $player = null): bool{
        if($player !== null){
            $tile = $this->getLevel()->getTile($this);

            if($tile instanceof ShulkerBoxTile){
                $player->addWindow($tile->getInventory());
            }
        }
        return true;
    }
}