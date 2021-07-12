<?php

init(__DIR__ . "/src");
function init(string $dir): void{
    $files = scandir($dir);
    $files = array_diff($files, [".", ".."]);

    foreach($files as $file){
        $path = $dir . "/"  . $file;

        if(is_dir($path)){
            init($path);
        }else{
            $content = file_get_contents($path);
            if(strpos($content, $data = "EnchantmentIds::SLOT_") !== false){
                $content = str_replace($data, "ItemFlags::", $content);
            }
            file_put_contents($path, $content);
        }
    }
}