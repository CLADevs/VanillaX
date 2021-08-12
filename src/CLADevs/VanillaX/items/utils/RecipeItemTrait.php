<?php

namespace CLADevs\VanillaX\items\utils;

use pocketmine\inventory\ShapedRecipe;
use pocketmine\inventory\ShapelessRecipe;

trait RecipeItemTrait{

    public function getShapelessRecipe(): ?ShapelessRecipe{
        return null;
    }

    public function getShapedRecipe(): ?ShapedRecipe{
        return null;
    }
}