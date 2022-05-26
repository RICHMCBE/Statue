<?php

declare(strict_types=1);

namespace DOHWI\Statue\Listener;

use DOHWI\Statue\Statue;
use pocketmine\nbt\tag\ByteTag;
use DOHWI\Statue\Util\StatueUtil;
use DOHWI\Statue\Queue\StatueQueue;
use DOHWI\Statue\Task\GiveStatueRewardsTask;

use pocketmine\Server;
use pocketmine\event\Listener;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerDropItemEvent;

use function count;
use function explode;

final class EventListener implements Listener
{
    public function __construct(private TaskScheduler $scheduler) {}

    public function onDrop(PlayerDropItemEvent $event) : void
    {
        if(Statue::$datas['STATUE_STATE'] === false) return;
        if(!isset(Statue::$datas['STATUE_POSITION']['WORLD'])) return;
        $item = $event->getItem();
        $player = $event->getPlayer();
        $idMeta = "{$item->getId()}:{$item->getMeta()}";
        if(!isset(Statue::$statue_items[$idMeta])) return;
        if($player->getPosition()->getWorld()->getFolderName() !== Statue::$datas['STATUE_POSITION']['WORLD']) return;
        $pos1 = explode(':',Statue::$datas['STATUE_POSITION']['POS1']);
        $pos2 = explode(':',Statue::$datas['STATUE_POSITION']['POS2']);
        $ppos = $player->getPosition();
        if($ppos->x >= $pos1[0] && $ppos->y >= $pos1[1] && $ppos->z >= $pos1[2] && $ppos->x <= $pos2[0] && $ppos->y <= $pos2[1] && $ppos->z <= $pos2[2])
        {
            $event->cancel();
            $player->getInventory()->removeItem($item->setCount(1));
            $guage = Statue::$statue_items[$idMeta];
            $afterGuage = Statue::$datas['STATUE_GUAGE'] += $guage;
            if($afterGuage >= 100)
            {
                Server::getInstance()->broadcastMessage(Statue::PREFIX.'30초 뒤, 제단 헌납 보상이 지급됩니다.');
                Server::getInstance()->broadcastMessage(Statue::PREFIX.'인벤토리를 §a'.(count(Statue::$datas['STATUE_REWARDS'])).'칸 §7이상 비워주세요.');
                $this->scheduler->scheduleDelayedTask(new GiveStatueRewardsTask(),20*30);
                Statue::$datas['STATUE_GUAGE'] = 0;
                return;
            }
            Server::getInstance()->broadcastMessage("§l§a[제단 헌납] §r§a{$player->getName()} §f| 게이지 §a{$guage} 증가\n§f- 제단 활성화까지 남은 게이지: §a".(100-$afterGuage)."§r");
        }
    }

    // POS1 감지
    public function onTouch(PlayerInteractEvent $event) : void
    {
        if($event->getAction() !== $event::RIGHT_CLICK_BLOCK) return;
        $item = $event->getItem();
        if(!StatueUtil::getStateEditTool()->equals($item)) return;
        $event->cancel();
        StatueQueue::$editors[$event->getPlayer()->getName()]['POS1'] = $event->getBlock()->getPosition();
        $event->getPlayer()->sendMessage(Statue::PREFIX.'1번 지점이 설정되었습니다.');
    }

    // POS2 감지
    public function onBreak(BlockBreakEvent $event) : void
    {
        $item = $event->getItem();
        if(!StatueUtil::getStateEditTool()->equals($item)) return;
        $event->cancel();
        StatueQueue::$editors[$event->getPlayer()->getName()]['POS2'] = $event->getBlock()->getPosition();
        $event->getPlayer()->sendMessage(Statue::PREFIX.'2번 지점이 설정되었습니다.');
    }
}
