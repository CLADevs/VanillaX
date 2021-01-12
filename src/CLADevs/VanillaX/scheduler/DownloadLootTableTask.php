<?php

namespace CLADevs\VanillaX\scheduler;

use CLADevs\VanillaX\VanillaX;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use ZipArchive;

class DownloadLootTableTask extends AsyncTask{

    const FILE_NAME = "Minecraft_Loots.zip";

    private string $dataPath;
    private string $destination;

    public function __construct(string $dataPath){
        $this->dataPath = $dataPath;
        $this->destination = $dataPath . DIRECTORY_SEPARATOR . self::FILE_NAME;
    }

    public function onRun(): void{
        if(!file_exists($this->destination)){
            file_put_contents($this->destination, fopen("https://aka.ms/behaviorpacktemplate", "r"));
        }
        $zip = new ZipArchive;
        if($zip->open($this->destination) === true){
            $files = [];
            for($i = 0; $i < $zip->numFiles; $i++){
                $fileName = $zip->getNameIndex($i);

                if(strpos($fileName, "loot_tables") !== false){
                    $files[] = $fileName;
                }
            }
            $zip->extractTo($this->dataPath, $files);
            $zip->close();
            Utils::recursiveUnlink($this->dataPath . self::FILE_NAME);
        }
    }

    public function onCompletion(Server $server): void{
        /** @var VanillaX|null $plugin */
        $plugin = $server->getPluginManager()->getPlugin("VanillaX");

        if($plugin !== null){
            $plugin->getEntityManager()->getLootManager()->initializeLootTable("loot_tables");
            $plugin->getLogger()->notice(TextFormat::GREEN . "Successfully downloaded loot tables");
        }
    }
}