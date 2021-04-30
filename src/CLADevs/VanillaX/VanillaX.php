<?php

namespace CLADevs\VanillaX;

use CLADevs\VanillaX\blocks\BlockManager;
use CLADevs\VanillaX\commands\CommandManager;
use CLADevs\VanillaX\enchantments\EnchantmentManager;
use CLADevs\VanillaX\entities\EntityManager;
use CLADevs\VanillaX\items\ItemManager;
use CLADevs\VanillaX\session\SessionManager;
use pocketmine\block\BlockFactory;
use pocketmine\plugin\PluginBase;
use ReflectionException;
use ReflectionProperty;

class VanillaX extends PluginBase{

    private static VanillaX $instance;

    private EntityManager $entityManager;
    private BlockManager $blockManager;
    private ItemManager $itemManager;
    private SessionManager $sessionManager;
    private EnchantmentManager $enchantmentManager;
    private CommandManager $commandManager;

    public function onLoad(): void{
        $this->saveDefaultConfig();
        self::$instance = $this;
        $this->entityManager = new EntityManager();
        $this->blockManager = new BlockManager();
        $this->itemManager = new ItemManager();
        $this->sessionManager = new SessionManager();
        $this->enchantmentManager = new EnchantmentManager();
        $this->commandManager = new CommandManager();
    }

    /**
     * @throws ReflectionException
     */
    public function onEnable(): void{
        $reflection = new ReflectionProperty(BlockFactory::class, "fullList");
        $reflection->setAccessible(true);
        $value = $reflection->getValue();
        $value->setSize(16384);
        $reflection->setValue(null, $value);
        BlockFactory::$light->setSize(16384);
        BlockFactory::$lightFilter->setSize(16384);
        BlockFactory::$solid->setSize(16384);
        BlockFactory::$hardness->setSize(16384);
        BlockFactory::$transparent->setSize(16384);
        BlockFactory::$diffusesSkyLight->setSize(16384);
        BlockFactory::$blastResistance->setSize(16384);

        $this->entityManager->startup();
        $this->blockManager->startup();
        $this->itemManager->startup();
        $this->enchantmentManager->startup();
        $this->commandManager->startup();
        $this->getServer()->getPluginManager()->registerEvents(new VanillaListener(), $this);
    }

    public function getFile(): string{
        return parent::getFile();
    }

    public function isPhar(): bool{
        return parent::isPhar();
    }

    public static function getInstance(): VanillaX{
        return self::$instance;
    }

    public function getEnchantmentManager(): EnchantmentManager{
        return $this->enchantmentManager;
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