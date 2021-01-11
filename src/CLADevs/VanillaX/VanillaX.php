<?php

namespace CLADevs\VanillaX;

use CLADevs\VanillaX\blocks\BlockManager;
use CLADevs\VanillaX\entities\EntityManager;
use CLADevs\VanillaX\items\ItemManager;
use CLADevs\VanillaX\session\SessionManager;
use pocketmine\plugin\PluginBase;
use ReflectionException;

class VanillaX extends PluginBase{

    private static VanillaX $instance;

    private EntityManager $entityManager;
    private BlockManager $blockManager;
    private ItemManager $itemManager;
    private SessionManager $sessionManager;

    public function onLoad(): void{
        foreach($this->getResources() as $path => $resource){
            $this->saveResource($path);
        }
        self::$instance = $this;
        $this->entityManager = new EntityManager();
        $this->blockManager = new BlockManager();
        $this->itemManager = new ItemManager();
        $this->sessionManager = new SessionManager();
    }

    /**
     * @throws ReflectionException
     */
    public function onEnable(): void{
        $this->entityManager->startup();
        $this->blockManager->startup();
        $this->itemManager->startup();
        $this->getServer()->getPluginManager()->registerEvents(new VanillaListener(), $this);
    }

    public static function getInstance(): VanillaX{
        return self::$instance;
    }

    public function getSessionManager(): SessionManager{
        return $this->sessionManager;
    }

    public function getItemManager(): ItemManager{
        return $this->itemManager;
    }

    public function getBlockManager(): BlockManager{
        return $this->blockManager;
    }

    public function getEntityManager(): EntityManager{
        return $this->entityManager;
    }
}