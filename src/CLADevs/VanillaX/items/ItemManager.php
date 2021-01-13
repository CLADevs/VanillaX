<?php

namespace CLADevs\VanillaX\items;

use CLADevs\VanillaX\items\types\ArmorStandItem;
use CLADevs\VanillaX\items\types\ElytraItem;
use CLADevs\VanillaX\items\types\EndCrystalItem;
use CLADevs\VanillaX\items\types\EnderEyeItem;
use CLADevs\VanillaX\items\types\FireChargeItem;
use CLADevs\VanillaX\items\types\FireworkRocketItem;
use CLADevs\VanillaX\items\types\FireworkStarItem;
use CLADevs\VanillaX\items\types\HorseArmorItem;
use CLADevs\VanillaX\items\types\LeadItem;
use CLADevs\VanillaX\items\types\MapItem;
use CLADevs\VanillaX\items\types\MinecartItem;
use CLADevs\VanillaX\items\types\NameTagItem;
use CLADevs\VanillaX\items\types\SaddleItem;
use CLADevs\VanillaX\items\types\TridentItem;
use CLADevs\VanillaX\items\types\TurtleHelmetItem;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;

class ItemManager{

    public function startup(): void{
        //TODO Armor function
        self::register(new ElytraItem(), true);
        self::register(new SaddleItem(), true);
        self::register(new ArmorStandItem(), true);
        self::register(new Item(ItemIds::KELP), true); //ITEM
        self::register(new Item(ItemIds::CARROT_ON_A_STICK), true); //ITEM
        self::register(new Item(ItemIds::ENCHANTED_BOOK)); //ITEM
        self::register(new EnderEyeItem(), true);
        self::register(new FireChargeItem(), true);
        self::register(new FireworkRocketItem(), true);
        self::register(new FireworkStarItem(), true);
        self::register(new LeadItem(), true);
        self::register(new NameTagItem(), true);
        self::register(new EndCrystalItem(), true);
        self::register(new TridentItem(), true);
        self::register(new TurtleHelmetItem(), true);

        self::register(new MinecartItem(ItemIds::MINECART_WITH_CHEST));
        self::register(new MinecartItem(ItemIds::MINECART_WITH_TNT));
        self::register(new MinecartItem(ItemIds::MINECART_WITH_HOPPER));
        self::register(new MinecartItem(ItemIds::MINECART_WITH_COMMAND_BLOCK));
        self::register(new MapItem(MapItem::FILLED_MAP));
        self::register(new MapItem(MapItem::EMPTY_MAP), true);

        for($i = 416; $i <= 419; $i++){
            self::register(new HorseArmorItem($i), true);
        }
    }
    
    public static function register(Item $item, bool $creative = false, bool $overwrite = true): void{
        ItemFactory::registerItem($item, $overwrite);
        if($creative && !Item::isCreativeItem($item)) Item::addCreativeItem($item);
    }
}