<?php

namespace CLADevs\VanillaX\world\sounds;

use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\world\sound\Sound;

class BellSound implements Sound{

    public function encode(?Vector3 $pos): array{
        return [LevelSoundEventPacket::create(LevelSoundEventPacket::SOUND_BLOCK_BELL_HIT, $pos)];
    }
}