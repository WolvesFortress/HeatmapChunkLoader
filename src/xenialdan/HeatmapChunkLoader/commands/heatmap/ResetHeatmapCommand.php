<?php

declare(strict_types=1);

namespace xenialdan\HeatmapChunkLoader\commands\heatmap;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;
use UnderflowException;
use xenialdan\HeatmapChunkLoader\Loader;

class ResetHeatmapCommand extends BaseSubCommand
{
    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     */
    protected function prepare(): void
    {
        $this->setPermission("heatmap.reset");
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param mixed[] $args
     * @throws UnderflowException
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TF::RED . 'error.runingame');
            return;
        }
        /** @var Player $sender */
        Loader::getInstance()->getHeatmap()->reset();
        $sender->sendMessage(TF::GREEN . 'Heatmap data was reset');
    }
}