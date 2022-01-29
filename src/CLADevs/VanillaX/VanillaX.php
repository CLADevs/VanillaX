<?php

namespace CLADevs\VanillaX;

use CLADevs\VanillaX\blocks\BlockManager;
use CLADevs\VanillaX\commands\CommandManager;
use CLADevs\VanillaX\configuration\SettingManager;
use CLADevs\VanillaX\enchantments\EnchantmentManager;
use CLADevs\VanillaX\entities\EntityManager;
use CLADevs\VanillaX\inventories\InventoryManager;
use CLADevs\VanillaX\items\ItemManager;
use CLADevs\VanillaX\listeners\ListenerManager;
use CLADevs\VanillaX\session\SessionManager;
use CLADevs\VanillaX\world\WorldManager;
use pocketmine\plugin\PluginBase;
use ReflectionException;

final class VanillaX extends PluginBase{

    private static VanillaX $instance;

    private EntityManager $entityManager;
    private ItemManager $itemManager;
    private EnchantmentManager $enchantmentManager;
    private BlockManager $blockManager;
    private SessionManager $sessionManager;
    private CommandManager $commandManager;
    private InventoryManager $inventoryManager;
    private ListenerManager $listenerManager;
    private WorldManager $worldManager;
    private SettingManager $settingManager;

    public function onLoad(): void{
        self::$instance = $this;
        $this->entityManager = new EntityManager();
        $this->itemManager = new ItemManager();
        $this->enchantmentManager = new EnchantmentManager();
        $this->blockManager = new BlockManager();
        $this->sessionManager = new SessionManager();
        $this->commandManager = new CommandManager();
        $this->inventoryManager = new InventoryManager();
        $this->listenerManager = new ListenerManager();
        $this->worldManager = new WorldManager();
        $this->settingManager = new SettingManager();
    }

    /**
     * @throws ReflectionException
     */
    public function onEnable(): void{
        $this->enchantmentManager->startup();
        $this->entityManager->startup();
        $this->blockManager->startup();
        $this->itemManager->startup();
        $this->commandManager->startup();
        $this->listenerManager->startup();
        $this->worldManager->startup();
    }

    public function getFile(): string{
        return parent::getFile();
    }

    public static function getInstance(): VanillaX{
        return self::$instance;
    }

    public function getWorldManager(): WorldManager{
        return $this->worldManager;
    }

    public function getEntityManager(): EntityManager{
        return $this->entityManager;
    }

    public function getItemManager(): ItemManager{
        return $this->itemManager;
    }

    public function getEnchantmentManager(): EnchantmentManager{
        return $this->enchantmentManager;
    }

    public function getSessionManager(): SessionManager{
        return $this->sessionManager;
    }

    public function getBlockManager(): BlockManager{
        return $this->blockManager;
    }

    public function getInventoryManager(): InventoryManager{
        return $this->inventoryManager;
    }

    public function getCommandManager(): CommandManager{
        return $this->commandManager;
    }

    public function getSettingManager(): SettingManager{
        return $this->settingManager;
    }
}