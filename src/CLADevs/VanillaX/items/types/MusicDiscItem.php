<?php

namespace CLADevs\VanillaX\items\types;

use CLADevs\VanillaX\utils\item\NonAutomaticCallItemTrait;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

class MusicDiscItem extends Item implements NonAutomaticCallItemTrait{

    const RECORD_PIGSTEP = 759;

    private ?int $soundId;

    public function __construct(int $id, string $name = "Unknown", ?int $soundId = null){
        parent::__construct(new ItemIdentifier($id, 0), $name);
        $this->soundId = $soundId;
    }

    public function getSoundId(): ?int{
        return $this->soundId;
    }

    public static function getRecordName(int $id, bool $returnNull = false): ?string{
        switch($id){
            case ItemIds::RECORD_CAT:
                $name = "cat";
                break;
            case ItemIds::RECORD_BLOCKS:
                $name = "blocks";
                break;
            case ItemIds::RECORD_CHIRP:
                $name = "chirp";
                break;
            case ItemIds::RECORD_FAR:
                $name = "far";
                break;
            case ItemIds::RECORD_MALL:
                $name = "mall";
                break;
            case ItemIds::RECORD_MELLOHI:
                $name = "mellohi";
                break;
            case ItemIds::RECORD_STAL:
                $name = "stal";
                break;
            case ItemIds::RECORD_STRAD:
                $name = "strad";
                break;
            case ItemIds::RECORD_WARD:
                $name = "ward";
                break;
            case ItemIds::RECORD_11:
                $name = "11";
                break;
            case ItemIds::RECORD_WAIT:
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
            case ItemIds::RECORD_CHIRP:
            case ItemIds::RECORD_CAT:
                return (60 * 3) + 5;
            case ItemIds::RECORD_BLOCKS:
                return (60 * 5) + 45;
            case ItemIds::RECORD_FAR:
                return (60 * 2) + 54;
            case ItemIds::RECORD_MALL:
                return (60 * 3) + 17;
            case ItemIds::RECORD_MELLOHI:
                return 60 + 36;
            case ItemIds::RECORD_STAL:
                return (60 * 2) + 30;
            case ItemIds::RECORD_STRAD:
                return (60 * 3) + 8;
            case ItemIds::RECORD_WARD:
                return (60 * 4) + 11;
            case ItemIds::RECORD_11:
                return 60 + 11;
            case ItemIds::RECORD_WAIT:
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