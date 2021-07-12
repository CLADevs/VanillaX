<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\object\ArmorStandEntity;
use CLADevs\VanillaX\entities\object\ChestMinecartEntity;
use CLADevs\VanillaX\entities\object\CommandBlockMinecartEntity;
use CLADevs\VanillaX\entities\object\HopperMinecartEntity;
use CLADevs\VanillaX\entities\object\MinecartEntity;
use CLADevs\VanillaX\entities\object\TNTMinecartEntity;
use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\block\BaseRail;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\math\Vector3;
use pocketmine\player\Player;

class MinecartItem extends Item implements NonAutomaticCallItemTrait{

    public function __construct(int $id, string $name = "Unknown"){
        if($name === "Unknown"){
            $name = "Minecart";
        }else{
            $name = "Minecart with " . $name;
        }
        parent::__construct(new ItemIdentifier($id, 0), $name);
    }

    public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): bool{
        if($blockClicked instanceof BaseRail){
            $args = [$player->getLevel(), ArmorStandEntity::createBaseNBT($blockReplace->add(0.5, 0, 0.5))];
            $entity = null;
            switch($this->getMinecartBlock()){
                case self::AIR:
                    $entity = new MinecartEntity(...$args);
                    break;
                case self::CHEST:
                    $entity = new ChestMinecartEntity(...$args);
                    break;
                case self::TNT:
                    $entity = new TNTMinecartEntity(...$args);
                    break;
                case self::HOPPER_BLOCK:
                    $entity = new HopperMinecartEntity(...$args);
                    break;
                case self::COMMAND_BLOCK:
                    $entity = new CommandBlockMinecartEntity(...$args);
                    break;
            }
            if($entity !== null){
                $entity->spawnToAll();
                if($player->isSurvival() || $player->isAdventure()) $this->pop();
            }
        }
        return true;
    }

    public function getMinecartBlock(int $id = null): int{
        if($id === null) $id = $this->id;
        switch($id){
            case self::MINECART_WITH_CHEST:
                return self::CHEST;
            case self::MINECART_WITH_TNT:
                return self::TNT;
            case self::MINECART_WITH_HOPPER:
                return self::HOPPER_BLOCK;
            case self::MINECART_WITH_COMMAND_BLOCK:
                return self::COMMAND_BLOCK;
        }
        return self::AIR;
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}