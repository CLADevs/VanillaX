<?php

namespace CLADevs\VanillaX\configuration;

use CLADevs\VanillaX\configuration\features\BlockFeature;
use CLADevs\VanillaX\configuration\features\CommandFeature;
use CLADevs\VanillaX\configuration\features\EnchantmentFeature;
use CLADevs\VanillaX\configuration\features\ItemFeature;
use CLADevs\VanillaX\configuration\features\MobFeature;
use pocketmine\utils\SingletonTrait;

class SettingManager{
    use SingletonTrait;

    private Setting $setting;
    private BlockFeature $blockFeature;
    private CommandFeature $commandFeature;
    private EnchantmentFeature $enchantmentFeature;
    private ItemFeature $itemFeature;
    private MobFeature $mobFeature;

    public function __construct(){
        self::setInstance($this);
        $this->setting = new Setting();
        $this->blockFeature = new BlockFeature();
        $this->commandFeature = new CommandFeature();
        $this->enchantmentFeature = new EnchantmentFeature();
        $this->itemFeature = new ItemFeature();
        $this->mobFeature = new MobFeature();
    }
}