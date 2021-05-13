<?php

namespace CLADevs\VanillaX;

use CLADevs\VanillaX\blocks\BlockManager;
use CLADevs\VanillaX\commands\CommandManager;
use CLADevs\VanillaX\enchantments\EnchantmentManager;
use CLADevs\VanillaX\entities\EntityManager;
use CLADevs\VanillaX\items\ItemManager;
use CLADevs\VanillaX\network\NetworkManager;
use CLADevs\VanillaX\session\SessionManager;
use CLADevs\VanillaX\utils\Utils;
use pocketmine\block\BlockFactory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\network\mcpe\protocol\BatchPacket;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\PacketPool;
use pocketmine\network\mcpe\protocol\types\PotionTypeRecipe;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\timings\Timings;
use ReflectionException;
use ReflectionProperty;

class VanillaX extends PluginBase{

    private const BREW_CONVERSATION = [
        424 => 373,
        392 => 348,
        551 => 438,
        552 => 441,
        426 => 376,
        427 => 377,
        422 => 370,
        283 => 396,
        428 => 378,
        564 => 470,
        267 => 462,
        518 => 414,
        432 => 331,
        278 => 382,
        563 => 469,
        371 => 331,
        294 => 372
    ];

    private static VanillaX $instance;

    private EnchantmentManager $enchantmentManager;
    private EntityManager $entityManager;
    private BlockManager $blockManager;
    private ItemManager $itemManager;
    private SessionManager $sessionManager;
    private CommandManager $commandManager;
    private NetworkManager $networkManager;

    /** @var PotionTypeRecipe[] */
    private array $potionTypeRecipes = [];

    public function onLoad(): void{
        $this->saveDefaultConfig();
        self::$instance = $this;
        $this->enchantmentManager = new EnchantmentManager();
        $this->entityManager = new EntityManager();
        $this->blockManager = new BlockManager();
        $this->itemManager = new ItemManager();
        $this->sessionManager = new SessionManager();
        $this->commandManager = new CommandManager();
        $this->networkManager = new NetworkManager();
    }

    /**
     * @throws ReflectionException
     */
    public function onEnable(): void{
        /** 4.0 Blocks Size */
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

        /** Brewing Stand */
        $reflection = new ReflectionProperty($craftingManager = $this->getServer()->getCraftingManager(), "craftingDataCache");
        $reflection->setAccessible(true);
        $reflection->setValue($craftingManager, $this->getCraftingDataPacket());

        $this->enchantmentManager->startup();
        $this->entityManager->startup();
        $this->blockManager->startup();
        $this->itemManager->startup();
        $this->commandManager->startup();
        $this->networkManager->startup();
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

    public function getNetworkManager(): NetworkManager{
        return $this->networkManager;
    }

    private function getCraftingDataPacket(): ?BatchPacket{
        $batch = $this->getServer()->getCraftingManager()->getCraftingDataPacket();
        $packet = null;

        foreach($batch->getPackets() as $buff){
            $pk = PacketPool::getPacket($buff);

            if($pk instanceof CraftingDataPacket){
                $packet = clone $pk;
                break;
            }
        }
        if($packet instanceof CraftingDataPacket){
            Timings::$craftingDataCacheRebuildTimer->startTiming();
            foreach(json_decode(file_get_contents(Utils::getResourceFile("brewing_recipes.json")), true) as $key => $i){
                $packet->potionTypeRecipes[] = new PotionTypeRecipe($i[0], $i[1], $i[2], $i[3], $i[4], $i[5]);
                $potion = new PotionTypeRecipe(self::convertPotionId($i[0]), self::convertPotionId($i[1]), self::convertPotionId($i[2]), self::convertPotionId($i[3]), self::convertPotionId($i[4]), self::convertPotionId($i[5]));
                $this->potionTypeRecipes[$potion->getInputItemId() . ":" . $potion->getInputItemMeta() . ":" . $potion->getIngredientItemId() . ":" . $potion->getIngredientItemMeta()] = clone $potion;
            }
            $packet->encode();

            $batch = new BatchPacket();
            $batch->addPacket($packet);
            $batch->setCompressionLevel(Server::getInstance()->networkCompressionLevel);
            $batch->encode();
            Timings::$craftingDataCacheRebuildTimer->stopTiming();
        }
        return $batch;
    }

    public function getBrewingOutput(Item $input, Item $ingredient): ?Item{
        $potion = $this->potionTypeRecipes[$input->getId() . ":" . $input->getDamage() . ":" . $ingredient->getId() . ":" . $ingredient->getDamage()] ?? null;

        if($potion instanceof PotionTypeRecipe){
            return ItemFactory::get($potion->getOutputItemId(), $potion->getOutputItemMeta(), $input->getCount());
        }
        return null;
    }

    private static function convertPotionId(int $value): int{
        return self::BREW_CONVERSATION[$value] ?? $value;
    }

}