<?php

namespace CLADevs\VanillaX\utils;

use CLADevs\VanillaX\VanillaX;

class Utils{

    public static function getResourceFile(string $file): string{
        return str_replace(["\\utils", "/utils"], DIRECTORY_SEPARATOR . "resources", __DIR__) . DIRECTORY_SEPARATOR . $file;
    }

    public static function getMinecraftDataPath(): string{
        return VanillaX::getInstance()->getDataFolder() . "data" . DIRECTORY_SEPARATOR;
    }

    public static function getVanillaXPath(): string{
        if(VanillaX::getInstance()->isPhar()){
            $path = self::removeLastDirectory(VanillaX::getInstance()->getDescription()->getMain());
            $path = VanillaX::getInstance()->getFile() . "src" . DIRECTORY_SEPARATOR . $path;
            return $path;
        }else{
            return (self::removeLastDirectory( __DIR__));
        }
    }

    public static function callDirectory(string $directory, callable $callable): void{
        $dirname = self::getVanillaXPath();
        $path = $dirname . DIRECTORY_SEPARATOR . $directory;
        $path = str_replace(["phar:///", "phar://", "//", "phar:\\\\", "\\"], ["phar:\\\\/", "phar:\\\\", "/", "phar://", "/"], $path);
        $phar = VanillaX::getInstance()->isPhar();

        foreach(array_diff(scandir($path), [".", ".."]) as $file){
            if(is_dir($path . DIRECTORY_SEPARATOR . $file)){
                self::callDirectory($directory . DIRECTORY_SEPARATOR . $file, $callable);
            }else{
                $i = explode(".", $file);
                $extension = $i[count($i) - 1];

                if($extension === "php"){
                    $name = $i[0];
                    $namespace = "";
                    $i = explode(DIRECTORY_SEPARATOR, str_replace(getcwd() . DIRECTORY_SEPARATOR, "", $dirname));
                    for($v = 0; $v <= ($phar ? 1 : 2); $v++){
                        unset($i[$v]);
                    }
                    foreach($i as $key => $string){
                        $namespace .= $string . DIRECTORY_SEPARATOR;
                    }
                    $namespace .= $directory . DIRECTORY_SEPARATOR . $name;
                    $namespace = str_replace("/", "\\", $namespace);
                    if(($pos = strpos($namespace, "src\\")) !== false){
                        $namespace = substr($namespace, $pos + 4);
                    }
                    $callable($namespace);
                }
            }
        }
    }

    private static function removeLastDirectory(string $str, int $loop = 1): string{
        $delimiter = strpos($str, DIRECTORY_SEPARATOR) ? DIRECTORY_SEPARATOR : "\\";

        for($i = 0; $i < $loop; $i++){
            $exp = explode($delimiter, $str);
            unset($exp[array_key_last($exp)]);
            $str = implode($delimiter, $exp);
        }
        return $str;
    }
}