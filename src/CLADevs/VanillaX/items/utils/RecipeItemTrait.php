<?php

namespace CLADevs\VanillaX\items\utils;

use pocketmine\crafting\ShapedRecipe;
use pocketmine\crafting\ShapelessRecipe;

trait RecipeItemTrait{

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return null;
    }

    public function getShapedRecipe(): ?ShapedRecipe{
        return null;
    }
}