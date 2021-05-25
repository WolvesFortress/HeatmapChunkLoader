<?php

declare(strict_types=1);

namespace xenialdan\HeatmapChunkLoader;

use pocketmine\Server;
use pocketmine\world\ChunkLoader;
use pocketmine\world\World;
use RuntimeException;

class HeatmapEntry implements ChunkLoader
{
    public string $world;
    public int $countLoad = 0;
    public float $lastModified;

    /**
     * HeatmapEntry constructor.
     * @param World $world
     */
    public function __construct(World $world)
    {
        $this->world = $world->getFolderName();
    }

    /**
     * @return World
     * @throws RuntimeException
     */
    public function getWorld(): World
    {
        return Server::getInstance()->getWorldManager()->getWorldByName($this->world);
    }
}