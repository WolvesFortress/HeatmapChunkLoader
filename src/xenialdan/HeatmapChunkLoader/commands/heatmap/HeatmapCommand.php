<?php

declare(strict_types=1);

namespace xenialdan\HeatmapChunkLoader\commands\heatmap;

use CortexPE\Commando\BaseCommand;
use InvalidArgumentException;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat as TF;

class HeatmapCommand extends BaseCommand
{
    /**
     * This is where all the arguments, permissions, sub-commands, etc would be registered
     * @throws InvalidArgumentException
     */
    protected function prepare(): void
    {
        $this->registerSubCommand(new ResetHeatmapCommand("reset", "Reset the heatmap data"));
        $this->registerSubCommand(new ListHeatmapCommand("list", "List the heatmap data"));
        $this->setPermission("heatmap");
    }

    /**
     * @param CommandSender $sender
     * @param string $aliasUsed
     * @param mixed[] $args
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TF::RED . 'error.runingame');
            return;
        }
        /** @var Player $sender */
        $sender->sendMessage($this->getUsage());
    }
}
