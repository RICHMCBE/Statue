<?php

declare(strict_types=1);

namespace DOHWI\Statue\Command;

use DOHWI\Statue\Statue;

use pocketmine\player\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

final class StatueDisableCommand extends Command
{
    public function __construct()
    {
        parent::__construct('제단컨트롤','§r§7제단이 활성화 / 비활성화됩니다');
        $this->setPermission('statue.op');
    }

    public function execute(CommandSender $sender,string $commandLabel,array $args) : void
    {
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;
        Statue::$datas['STATUE_STATE'] = !Statue::$datas['STATUE_STATE'];
        $state = Statue::$datas['STATUE_STATE'] ?'활성화' : '비활성화';
        $sender->sendMessage(Statue::PREFIX."제단이 §a{$state}§7되었어요");
    }
}
