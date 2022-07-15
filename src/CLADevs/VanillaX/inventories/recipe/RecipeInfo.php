<?php

namespace CLADevs\VanillaX\inventories\recipe;

use pocketmine\item\Item;

class RecipeInfo{

    public function __construct(private Item $input, private Item $material, private Item $output){
    }

    public function getInput(): Item{
        return $this->input;
    }

    public function getMaterial(): Item{
        return $this->material;
    }

    public function getOutput(): Item{
        return $this->output;
    }
}