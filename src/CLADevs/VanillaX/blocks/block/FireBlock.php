<?php

namespace CLADevs\VanillaX\blocks\block;

use CLADevs\VanillaX\items\LegacyItemIds;
use CLADevs\VanillaX\utils\item\NonCreativeItemTrait;
use CLADevs\VanillaX\world\gamerule\GameRule;
use CLADevs\VanillaX\world\gamerule\GameRuleManager;
use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Fire;
use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;

class FireBlock extends Fire implements NonCreativeItemTrait{

    public function __construct(){
        parent::__construct(new BlockIdentifier(BlockLegacyIds::FIRE, 0), "Fire Block", BlockBreakInfo::instant());
    }

    public function onRandomTick(): void{
        if(!GameRuleManager::getInstance()->getValue(GameRule::DO_FIRE_TICK, $this->getPosition()->getWorld())){
            return;
        }
        parent::onRandomTick();
    }

    public function onEntityInside(Entity $entity): bool{
        if($entity instanceof ItemEntity){
            $item = $entity->getItem();

            if(in_array($item->getId(), [
                LegacyItemIds::NETHERITE_HELMET,
                LegacyItemIds::NETHERITE_CHESTPLATE,
                LegacyItemIds::NETHERITE_LEGGINGS,
                LegacyItemIds::NETHERITE_BOOTS,
                LegacyItemIds::NETHERITE_SWORD,
                LegacyItemIds::NETHERITE_AXE,
                LegacyItemIds::NETHERITE_PICKAXE,
                LegacyItemIds::NETHERITE_SHOVEL,
                LegacyItemIds::NETHERITE_HOE,
                LegacyItemIds::NETHERITE_SCRAP,
                LegacyItemIds::NETHERITE_INGOT
            ])){
                return false;
            }
        }
        return parent::onEntityInside($entity);
    }
}