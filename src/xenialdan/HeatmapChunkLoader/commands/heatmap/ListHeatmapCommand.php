<?php

declare(strict_types=1);

namespace xenialdan\HeatmapChunkLoader\commands\heatmap;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use pocketmine\world\World;
use UnderflowException;
use xenialdan\HeatmapChunkLoader\HeatmapEntry;
use xenialdan\HeatmapChunkLoader\Loader;

class ListHeatmapCommand extends BaseSubCommand
{
    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
        $this->setPermission("heatmap.list");
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param mixed[] $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
//        if (!$sender instanceof Player) {
//            $sender->sendMessage(TF::RED . 'error.runingame');
//            return;
//        }
//        /** @var Player $sender */
        $values = [];
        $heatmap = Loader::getInstance()->getHeatmap()->getHeatmap();
        uasort($heatmap, function (HeatmapEntry $a, HeatmapEntry $b) {
            if ($a->countLoad === $b->countLoad) {
                return 0;
            }
            return ($a->countLoad < $b->countLoad) ? -1 : 1;
        });
        foreach ($heatmap as $hash => $value) {
            World::getXZ($hash, $x, $z);
            $values[] = "Hash: $hash Loaded: $value->countLoad Chunk: $x:$z";
        }
        $sender->sendMessage(implode(PHP_EOL, $values));
        unset($values, $heatmap);
    }
}