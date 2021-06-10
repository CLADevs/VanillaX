<?php

namespace CLADevs\VanillaX\scheduler;

use CLADevs\VanillaX\VanillaX;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use ZipArchive;

class DownloadMinecraftDataTask extends AsyncTask{

    const FILE_NAME = "Minecraft_Loots.zip";

    private string $dataPath;
    private string $destination;

    public function __construct(string $dataPath){
        $this->dataPath = $dataPath;
        $this->destination = $dataPath . DIRECTORY_SEPARATOR . self::FILE_NAME;
    }

    public function onRun(): void{
        if(!file_exists($this->destination)){
            file_put_contents($this->destination, fopen("https://aka.ms/behaviorpacktemplate", "r", false, stream_context_create(["ssl"=> ["verify_peer" => false, "verify_peer_name"=>false]])));
        }
        $zip = new ZipArchive;
        if($zip->open($this->destination) === true){
            $files = [];
            for($i = 0; $i < $zip->numFiles; $i++){
                $fileName = $zip->getNameIndex($i);

                foreach(["loot_tables", "trading"] as $folder){
                    if(strpos($fileName, $folder) !== false){
                        $passed = true;
                    }else{
                        $passed = false;
                        break;
                    }
                }
                if($passed){
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
            $plugin->getLogger()->notice(TextFormat::GREEN . "Successfully downloaded Minecraft data!");
        }
    }
}