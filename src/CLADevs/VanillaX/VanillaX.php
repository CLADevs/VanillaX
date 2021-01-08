<?php

namespace CLADevs\VanillaX;

use CLADevs\VanillaX\blocks\BlockManager;
use CLADevs\VanillaX\entities\EntityManager;
use pocketmine\plugin\PluginBase;
use ReflectionException;

class VanillaX extends PluginBase{

    private static VanillaX $instance;

    private EntityManager $entityManager;
    private BlockManager $blockManager;

    public function onLoad(): void{
        self::$instance = $this;
        $this->entityManager = new EntityManager();
        $this->blockManager = new BlockManager();
    }

    /**
     * @throws ReflectionException
     */
    public function onEnable(): void{
        $this->entityManager->startup();
        $this->blockManager->startup();
        $this->getServer()->getPluginManager()->registerEvents(new VanillaListener(), $this);
    }

    public static function getInstance(): VanillaX{
        return self::$instance;
    }
}