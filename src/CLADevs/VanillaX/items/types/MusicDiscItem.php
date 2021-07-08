<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\item\Item;

class MusicDiscItem extends Item implements NonAutomaticCallItemTrait{

    const RECORD_PIGSTEP = 759;

    private ?int $soundId;

    public function __construct(int $id, int $meta = 0, string $name = "Unknown", ?int $soundId = null){
        parent::__construct($id, $meta, $name);
        $this->soundId = $soundId;
    }

    public function getSoundId(): ?int{
        return $this->soundId;
    }

    public static function getRecordName(int $id, bool $returnNull = false): ?string{
        switch($id){
            case self::RECORD_CAT:
                $name = "cat";
                break;
            case self::RECORD_BLOCKS:
                $name = "blocks";
                break;
            case self::RECORD_CHIRP:
                $name = "chirp";
                break;
            case self::RECORD_FAR:
                $name = "far";
                break;
            case self::RECORD_MALL:
                $name = "mall";
                break;
            case self::RECORD_MELLOHI:
                $name = "mellohi";
                break;
            case self::RECORD_STAL:
                $name = "stal";
                break;
            case self::RECORD_STRAD:
                $name = "strad";
                break;
            case self::RECORD_WARD:
                $name = "ward";
                break;
            case self::RECORD_11:
                $name = "11";
                break;
            case self::RECORD_WAIT:
                $name = "wait";
                break;
            case self::RECORD_PIGSTEP:
                return "Lena Raine - Pigstep";
            default:
                $name = $returnNull ? null : "13";
                break;
        }
        return $name !== null ? "C418 - " . $name : null;
    }

    /**
     * @param int $id
     * @return int
     * This is in seconds
     */
    public static function getRecordLength(int $id): int{
        switch($id){
            case self::RECORD_CHIRP:
            case self::RECORD_CAT:
                return (60 * 3) + 5;
            case self::RECORD_BLOCKS:
                return (60 * 5) + 45;
            case self::RECORD_FAR:
                return (60 * 2) + 54;
            case self::RECORD_MALL:
                return (60 * 3) + 17;
            case self::RECORD_MELLOHI:
                return 60 + 36;
            case self::RECORD_STAL:
                return (60 * 2) + 30;
            case self::RECORD_STRAD:
                return (60 * 3) + 8;
            case self::RECORD_WARD:
                return (60 * 4) + 11;
            case self::RECORD_11:
                return 60 + 11;
            case self::RECORD_WAIT:
                return (60 * 3) + 51;
            case self::RECORD_PIGSTEP:
                return (60 * 2) + 28;
            default:
                return (60 * 2) + 58;
        }
    }

    public function getMaxStackSize(): int{
        return 1;
    }
}