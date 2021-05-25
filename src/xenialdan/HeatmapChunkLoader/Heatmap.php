<?php

declare(strict_types=1);

namespace xenialdan\HeatmapChunkLoader;

use pocketmine\world\World;

class Heatmap
{
    /** @var HeatmapEntry[] */
    private array $heatmap = [];
    public int $total = 0;
    public int $schedules = 0;
    private int $maxLoad = 0;

    /*public function __construct()
    {
        $this->heatmap = new Map();
    }*/

    public function reset(): void
    {
        //$this->heatmap->clear();
        $this->heatmap = [];
        $this->total = 0;
        $this->schedules = 0;
        $this->maxLoad = 0;
    }

    public function schedule(): void
    {
        $this->schedules++;
        $this->filter();
        #$this->total = 0;
    }

//    public function filter(): void
//    {
//        $period = Loader::getInstance()->getPeriod();
//        foreach ($this->heatmap as $hash => $value) {
//            // remove if not modified within the period
//            $old = microtime(true) - $value->lastModified > $period;
//            $unload = $this->checkUnload($hash);
//            if ($old || $unload) {
//                World::getXZ($hash, $x, $z);
//                Loader::getInstance()->getLogger()->debug("Unregistering loader at $x:$z, Reason:" . ($old ? " unchanged for $period" : '') . ($unload ? ' below treshold' : ''));
//                $value->getWorld()->unregisterChunkLoader($value, $x, $z);
//            }
//        }
//    }

    public function filter(): void
    {
        foreach ($this->heatmap as $hash => $entry) {
            World::getXZ($hash, $x, $z);
            if ($this->isInTreshold($entry)) {
                $entry->getWorld()->registerChunkLoader($entry, $x, $z);
                Loader::getInstance()->getLogger()->debug("Registering loader at $x:$z on load");
            } else {
                Loader::getInstance()->getLogger()->debug("Unregistering loader at $x:$z, Reason: below treshold");
                $entry->getWorld()->unregisterChunkLoader($entry, $x, $z);
            }
        }
    }

    public function addLoad(World $world, int $x, int $z): void
    {
        $this->total++;
        $hash = World::chunkHash($x, $z);
        $entry = $this->heatmap[$hash] ?? new HeatmapEntry($world);
        $entry->lastModified = microtime(true);
        $entry->countLoad++;
        $this->maxLoad = max($entry->countLoad, $this->maxLoad);
        $this->heatmap[$hash] = $entry;
    }

    private function isInTreshold(HeatmapEntry $entry): bool
    {
        //math is: load / max(loads) >= (100% - treshold)
        $treshold = (100 - Loader::getInstance()->getTreshold()) / 100;
        $entryTreshold = $entry->countLoad / $this->maxLoad;
        var_dump($treshold, $entryTreshold);
        return $entryTreshold >= $treshold;
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