<?php

namespace CLADevs\VanillaX\commands\sender;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Translatable;

class CommandBlockSender extends ConsoleCommandSender{

    private string $messages = "";

    public function sendMessage(Translatable|string $message): void{
        if($message instanceof Translatable){
            $message = $this->getLanguage()->translate($message);
        }else{
            $message = $this->getLanguage()->translateString($message);
        }
        $this->messages .= trim($message, "\r\n") . "\n";
    }

    public function getMessage(): string{
        return $this->messages;
    }

    public function getName(): string{
        return "Command Block";
    }
}