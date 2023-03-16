<?php

namespace CLADevs\VanillaX\utils;

use CLADevs\VanillaX\VanillaX;
use const pocketmine\BEDROCK_DATA_PATH;

class Utils{

    public static function getResourceFile(string $file): string{
        return str_replace(["\\utils", "/utils"], DIRECTORY_SEPARATOR . "resources", __DIR__) . DIRECTORY_SEPARATOR . $file;
    }

    public static function callDirectory(string $directory, callable $callable): void{
        $main = explode("\\", VanillaX::getInstance()->getDescription()->getMain());
        unset($main[array_key_last($main)]);
        $main = implode("/", $main);
        $directory = rtrim(str_replace(DIRECTORY_SEPARATOR, "/", $directory), "/");
        $dir = VanillaX::getInstance()->getFile() . "src/$main/" . $directory;

        foreach(array_diff(scandir($dir), [".", ".."]) as $file){
            $path = $dir . "/$file";
            $extension = pathinfo($path)["extension"] ?? null;

            if($extension === null){
                self::callDirectory($directory . "/" . $file, $callable);
            }elseif($extension === "php"){
                $namespaceDirectory = str_replace("/", "\\", $directory);
                $namespaceMain = str_replace("/", "\\", $main);
                $namespace = $namespaceMain . "\\$namespaceDirectory\\" . basename($file, ".php");
                $callable($namespace);
            }
        }
    }

    /**
     * @return int[]
     */
    public static function getBlockIdsMap(): array{
        return json_decode((string)file_get_contents(PATH . "vendor/pocketmine/bedrock-block-upgrade-schema/block_legacy_id_map.json"), true);
    }

    /**
     * @return int[]
     */
    public static function getItemIdsMap(): array{
        return json_decode((string)file_get_contents(PATH . "vendor/pocketmine/bedrock-item-upgrade-schema/item_legacy_id_map.json"), true);
    }

    public static function clamp(int|float $value, int|float $min, int|float $max): int|float{
        if($value < $min) return $min;
        if($value > $max) return $max;
        return $value;
    }
}
