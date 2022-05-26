<?php

declare(strict_types=1);

namespace DOHWI\Statue\Task;

use DOHWI\Statue\Statue;

use pocketmine\Server;
use pocketmine\item\Item;
use pocketmine\scheduler\Task;

final class GiveStatueRewardsTask extends Task
{
    public function onRun() : void
    {
        foreach(Server::getInstance()->getOnlinePlayers() as $player)
        {
            foreach(Statue::$datas['STATUE_REWARDS'] as $item)
            {
                $item = Item::jsonDeserialize($item);
                $player->getInventory()->addItem($item);
            }
            $player->sendMessage(Statue::PREFIX.'제단 헌납 보상이 지급되었습니다.');
        }
    }
}
