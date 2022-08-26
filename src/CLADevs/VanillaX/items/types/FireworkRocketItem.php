<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\entities\object\FireworkRocketEntity;
use CLADevs\VanillaX\session\Session;
use pocketmine\block\Block;
use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\player\Player;

class FireworkRocketItem extends Item{

    public function __construct(){
        parent::__construct(new ItemIdentifier(ItemIds::FIREWORKS, 0), "Firework Rocket");
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult{
        if($this->checkElytra($player) && $this->count > 0){
            if($player->hasFiniteResources()) $this->pop();
            return ItemUseResult::SUCCESS();
        }
        return ItemUseResult::FAIL();
    }

    public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector): ItemUseResult{
        if($this->count < 1){
            return ItemUseResult::FAIL();
        }
        if(!$this->checkElytra($player)){
            $location = Location::fromObject($blockReplace->getPosition()->add(0.5, 0, 0.5), $blockClicked->getPosition()->getWorld());
            $entity = new FireworkRocketEntity($location, $player);

            if(($this->getNamedTag()->getTag("Fireworks")) !== null){
                $entity->getNetworkProperties()->setCompoundTag(16, new CacheableNbt($this->getNamedTag()));
            }
            $entity->spawnToAll();
        }
        Session::playSound($player, "firework.launch");
        if($player->hasFiniteResources()) $this->pop();
        return ItemUseResult::SUCCESS();
    }

    public function checkElytra(Player $player): bool{
        if($player->getArmorInventory()->getChestplate() instanceof ElytraItem && $player->isGliding()){
            $player->setMotion($player->getDirectionVector()->multiply(1.6));
            return true;
        }
        return false;
    }
}