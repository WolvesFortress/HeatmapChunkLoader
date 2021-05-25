<?php

namespace xenialdan\HeatmapChunkLoader;

use pocketmine\event\Listener;
use pocketmine\event\world\ChunkLoadEvent;

class EventListener implements Listener
{
    /**
     * @param ChunkLoadEvent $event
     * @priority MONITOR
     */
    public function onChunkLoad(ChunkLoadEvent $event)
    {
        Loader::getInstance()->getHeatmap()->addLoad($event->getWorld(), $event->getChunkX(), $event->getChunkZ());
    }

    //Not called because chunkloaders cancel it anyways
//    public function onChunkUnload(ChunkUnloadEvent $event)
//    {
//        if (!Loader::getInstance()->getHeatmap()->checkUnload(World::chunkHash($event->getChunkX(), $event->getChunkZ()))) $event->cancel();
//    }
}