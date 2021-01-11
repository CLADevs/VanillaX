<?php

namespace CLADevs\VanillaX\entities\monster;

use CLADevs\VanillaX\entities\Entity;
use CLADevs\VanillaX\entities\LivingEntity;
use CLADevs\VanillaX\entities\traits\EntityAgeable;
use pocketmine\item\Durable;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\Random;

class DrownedEntity extends LivingEntity{

    public $width = 0.6;
    public $height = 1.9;

    const NETWORK_ID = self::DROWNED;

    private bool $spawnedNaturallyEquipped = false;

    protected function initEntity(): void{
        parent::initEntity();
        $this->ageable = new EntityAgeable($this, [0.3, 0.95], [0.6, 1.9]);
        $this->ageable->setCanBeBredByPlayer(false);
        //TODO
    }

    public function getName(): string{
        return "Drowned";
    }

    public function isSpawnedNaturallyEquipped(): bool{
        return $this->spawnedNaturallyEquipped;
    }

    public function getLootItems(Entity $killer): array{
        $random = new Random();
        $finalItems = [];

        $rottenFlesh = ItemFactory::get(ItemIds::ROTTEN_FLESH, 0, mt_rand(0, 2));
        if(($looting = $this->getKillerEnchantment($killer)) > 0){
            $rottenFlesh->setCount($rottenFlesh->getCount() + mt_rand(0, $looting));
        }
        $finalItems[] = $rottenFlesh;

        $goldIngotChance = 0.11 + (0.02 * $looting);
        if($random->nextFloat() < $goldIngotChance){
            $finalItems[] = ItemFactory::get(ItemIds::GOLD_INGOT, 0, 1);
        }

        $tridentChance = 0.25 + (0.4 * $looting);
        if($random->nextFloat() < $tridentChance){
            $trident = ItemFactory::get(ItemIds::TRIDENT);

            if($trident instanceof Durable){
                $trident->setDamage(mt_rand(0, $trident->getMaxDurability() - 1));
            }
            $finalItems[] = $trident;
        }

        //TODO Check if its holding Nautilus Shell in off hand
        //TODO Drop item hand item
        foreach($this->getArmorInventory()->getContents() as $item){
            $finalItems[] = $item;
        }
        return $finalItems;
    }

    public function getLootExperience(): int{
        if($this->ageable->isBaby()){
            return 12;
        }else{
            return 5 + ($this->spawnedNaturallyEquipped ? mt_rand(1, 3) : 0);
        }
    }
}