<?php

declare(strict_types=1);

namespace xenialdan\HeatmapChunkLoader;

use InvalidArgumentException;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\ChunkLoader;
use xenialdan\HeatmapChunkLoader\commands\heatmap\HeatmapCommand;

class Loader extends PluginBase
{
    /** @var self */
    private static Loader $instance;

    public Heatmap $heatmap;

    /**
     * Returns an instance of the plugin
     * @return self
     */
    public static function getInstance(): self
    {
        return self::$instance;
    }

    public function onLoad(): void
    {
        self::$instance = $this;
        $this->heatmap = new Heatmap();
        $this->reloadConfig();
        $period =  (int)($this->getPeriod()/20);
        $this->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function() : void{
            Loader::getInstance()->getHeatmap()->filter();
        }),$period,$period);
    }

    public function getPeriod(): int
    {
        $period = strtotime($this->getConfig()->get('period', '5 minutes'));
        if ($period === false) throw new InvalidArgumentException('Config period could not be parsed by strtotime. Check https://www.php.net/manual/en/function.strtotime.php');
        return $period;
    }

    //TODO add percentage treshold & max chunks setting
    public function getTreshold(): int
    {
        return $this->getConfig()->get('treshold', 3);
    }

    public function onEnable(): void
    {
        //dependencies
        //listeners
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
        //commands
        $this->getServer()->getCommandMap()->registerAll('HeatmapChunkLoader', [
            new HeatmapCommand($this, 'heatmap', 'Manage heatmap chunkloader'),
        ]);
    }

    /**
     * @return Heatmap
     */
    public function getHeatmap(): Heatmap
    {
        return $this->heatmap;
    }
}
