<?php

namespace CLADevs\VanillaX\inventories\recipe;

use pocketmine\item\Item;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;

class RecipesMap{

    const SHAPELESS = "shapeless";

    /**
     * @param string $type
     * @param string $id
     * @param RecipeInfo[] $recipes
     */
    public function __construct(private string $type, private string $id, private array $recipes){
    }

    public static function from(array $data): ?RecipesMap{
        $list = [];
        $type = $data["type"] ?? null;
        $id = $data["id"] ?? null;
        $recipes = $data["recipes"] ?? null;

        if(is_string($type) && is_string($id) && is_array($recipes)){
            $parser = function (?string $input): ?Item{
                if($input === null){
                    return null;
                }
                try{
                    return StringToItemParser::getInstance()->parse($input) ?? LegacyStringToItemParser::getInstance()->parse($input);
                }catch (LegacyStringToItemParserException){
                    return null;
                }
            };

            foreach($recipes as $recipe){
                $input = $parser($recipe["input"] ?? null);
                $material = $parser($recipe["material"] ?? null);
                $output = $parser($recipe["output"] ?? null);

                if($input !== null && $material !== null && $output !== null){
                    $list[] = new RecipeInfo($input, $material, $output);
                }
            }
            return new RecipesMap($type, $id, $list);
        }
        return null;
    }

    public function isShapeless(): bool{
        return $this->id === self::SHAPELESS;
    }

    public function getType(): string{
        return $this->type;
    }

    public function getId(): string{
        return $this->id;
    }

    /**
     * @return RecipeInfo[]
     */
    public function getRecipes(): array{
        return $this->recipes;
    }
}