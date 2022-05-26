<?php

declare(strict_types=1);

namespace DOHWI\Statue\Command;

use DOHWI\Statue\Statue;
use DOHWI\Statue\Queue\StatueQueue;

use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

use function min;
use function max;

final class SetStatuePositionCommand extends Command
{
    public function __construct()
    {
        parent::__construct('제단영역설정','§r§7설정한 범위를 제단 영역으로 설정합니다.');
        $this->setPermission('statue.op');
    }

    public function execute(CommandSender $sender,string $commandLabel,array $args) : void
    {
        if(!$sender instanceof Player) return;
        if(!$this->testPermission($sender)) return;
        if(!isset(StatueQueue::$editors[$sender->getName()]['POS1']) || !isset(StatueQueue::$editors[$sender->getName()]['POS2']))
        {
            $sender->sendMessage(Statue::PREFIX.'범위를 설정한 후, 다시 시도해 주세요.');
            return;
        }
        /** @var Position[] $positions */
        $positions = StatueQueue::$editors[$sender->getName()];
        if($positions['POS1']->getWorld()->getFolderName() !== $positions['POS2']->getWorld()->getFolderName())
        {
            $sender->sendMessage(Statue::PREFIX.'범위는 동일한 월드에 지정해 주세요.');
            return;
        }
        $sender->sendMessage(Statue::PREFIX.'지정한 영역을 제단 영역으로 설정하였습니다.');
        $pos1 = $positions['POS1'];
        $pos2  = $positions['POS2'];
        $min = new Vector3(min($pos1->x,$pos2->x),min($pos1->y,$pos2->y),min($pos1->z,$pos2->z));
        $max = new Vector3(max($pos1->x,$pos2->x),max($pos1->y,$pos2->y),max($pos1->z,$pos2->z));
        Statue::$datas['STATUE_POSITION'] = [
            'POS1' => self::positionToString($min),
            'POS2' => self::positionToString($max),
            'WORLD' => $positions['POS1']->getWorld()->getFolderName()
        ];
        unset(StatueQueue::$editors[$sender->getName()]);
    }

    private static function positionToString(Vector3 $vector) : string
    {
        return "{$vector->getX()}:{$vector->getY()}:{$vector->getZ()}";
    }
}
