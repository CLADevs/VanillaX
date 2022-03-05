<?php

namespace CLADevs\VanillaX\commands\sender;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Translatable;

class CommandBlockSender extends ConsoleCommandSender{

    private string $output = "";

    public function sendMessage(Translatable|string $message): void{
        if($message instanceof Translatable){
            $message = $this->getLanguage()->translate($message);
        }else{
            $message = $this->getLanguage()->translateString($message);
        }
        $this->output .= trim($message, "\r\n") . "\n";
    }

    public function getName(): string{
        return "Command Block";
    }

    public function getOutput(): string{
        return $this->output; 
    }
}
