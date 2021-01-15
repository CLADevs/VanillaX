<?php

namespace CLADevs\VanillaX\utils;

class Utils{

    public static function callDirectory(string $directory, callable $callable): void{
        $dirname = (self::removeLastDirectory( __DIR__, 2));
        $path = $dirname . DIRECTORY_SEPARATOR . $directory;

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
                    for($v = 0; $v <= 2; $v++){
                        unset($i[$v]);
                    }
                    foreach($i as $key => $string){
                        $namespace .= $string . DIRECTORY_SEPARATOR;
                    }
                    $namespace .= $directory . DIRECTORY_SEPARATOR . $name;
                    $callable($namespace);
                }
            }
        }
    }

    private static function removeLastDirectory(string $str, int $loop = 1): string{
        for($i = 0; $i < $loop; $i++){
            $i = explode(DIRECTORY_SEPARATOR, $str);
            unset($i[array_key_last($i)]);
            $str = implode(DIRECTORY_SEPARATOR, $i);
        }
        return $str;
    }
}