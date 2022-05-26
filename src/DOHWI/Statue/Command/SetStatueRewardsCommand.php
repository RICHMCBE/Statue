<?php

declare(strict_types=1);

namespace DOHWI\Statue\Command;

use DOHWI\Statue\Statue;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use function array_shift;

final class SetStatueRewardsCommand extends Command
{
    public function __construct()
    {
        parent::__construct('제단보상설정','§r§7제단 보상을 관리합니다.');
        $this->setPermission('statue.op');
    }

    public function execute(CommandSender $sender,string $commandLabel,array $args) : void
    {
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;
        $prefix = array_shift($args);
        if($prefix === '추가')
        {
            $item = $sender->getInventory()->getItemInHand();
            if($item->getId() === 0)
            {
                $sender->sendMessage(Statue::PREFIX.'공기는 보상으로 설정할 수 없습니다.');
                return;
            }
            Statue::$datas['STATUE_REWARDS'][] = $item->jsonSerialize();
            $sender->sendMessage(Statue::PREFIX.'제단 헌납 보상을 추가하였습니다.');
        } else if($prefix === '초기화')
        {
            $sender->sendMessage(Statue::PREFIX.'제단 헌납 보상을 초기화하였습니다.');
            Statue::$datas['STATUE_REWARDS'] = [];
        } else {
            $sender->sendMessage(Statue::PREFIX.'/제단보상설정 [추가/초기화]');
        }
    }
}
