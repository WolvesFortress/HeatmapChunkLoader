<?php

declare(strict_types=1);

namespace xenialdan\HeatmapChunkLoader;

use pocketmine\world\World;

class Heatmap
{
    /** @var HeatmapEntry[] */
    private array $heatmap = [];

    /*public function __construct()
    {
        $this->heatmap = new Map();
    }*/

    public function reset(): void
    {
        //$this->heatmap->clear();
        $this->heatmap = [];
    }

    public function filter(): void
    {
        $period = Loader::getInstance()->getPeriod();
        foreach ($this->heatmap as $hash => $value) {
            // remove if not modified within the period
            $old = microtime(true) - $value->lastModified > $period;
            $unload = $this->checkUnload($hash);
            if ($old || $unload) {
                World::getXZ($hash, $x, $z);
                Loader::getInstance()->getLogger()->debug("Unregistering loader at $x:$z, Reason:" . ($old ? " unchanged for $period" : '') . ($unload ? ' below treshold' : ''));
                $value->getWorld()->unregisterChunkLoader($value, $x, $z);
                unset($this->heatmap[$hash]);
            }
        }
    }

    public function addLoad(World $world, int $x, int $z): void
    {
        $hash = World::chunkHash($x, $z);
        $entry = $this->heatmap[$hash] ?? new HeatmapEntry($world);
        $entry->lastModified = microtime(true);
        $entry->countLoad++;
        $this->heatmap[$hash] = $entry;
        if ($entry->countLoad >= Loader::getInstance()->getTreshold()) {
            $world->registerChunkLoader($entry, $x, $z);
            Loader::getInstance()->getLogger()->debug("Registering loader at $x:$z");
        }
    }

    public function checkUnload($hash): bool
    {
        $entry = $this->getHeatmapEntry($hash);
        if ($entry === null) return true;
        // change last modified
        $entry->lastModified = microtime(true);
        $this->heatmap[$hash] = $entry;
        // unloads
        if ($entry->countLoad < Loader::getInstance()->getTreshold()) return true;
        return false;
    }

    /**
     * @return HeatmapEntry[]
     */
    public function getHeatmap(): array
    {
        return $this->heatmap;
    }

    /**
     * @param $hash
     * @return HeatmapEntry|null
     */
    public function getHeatmapEntry($hash): ?HeatmapEntry
    {
        return $this->heatmap[$hash] ?? null;
    }
}