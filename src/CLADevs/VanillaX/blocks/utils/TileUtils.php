<?php

namespace CLADevs\VanillaX\blocks\utils;

use pocketmine\level\Position;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Tile;

class TileUtils{

    /**
     * @param Position $position
     * @param string $id
     * @param string $class
     * @param NamedTag[] $NBTEntries
     * @return Tile|null
     */
    public static function  generateTile(Position $position, string $id, string $class, array $NBTEntries = []): ?Tile{
        $NBTEntries = array_merge($NBTEntries, [
            new StringTag(Tile::TAG_ID, $id),
            new IntTag(Tile::TAG_X, $position->x),
            new IntTag(Tile::TAG_Y, $position->y),
            new IntTag(Tile::TAG_Z, $position->z),
        ]);
        return new $class($position->getLevel(), new CompoundTag("", $NBTEntries));
    }
}