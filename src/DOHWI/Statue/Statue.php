<?php

declare(strict_types=1);

namespace DOHWI\Statue;

use DOHWI\Statue\Util\StatueUtil;
use DOHWI\Statue\Listener\EventListener;
use pocketmine\inventory\CreativeInventory;
use DOHWI\Statue\Command\StatueDisableCommand;
use DOHWI\Statue\Command\SetStatueRewardsCommand;
use DOHWI\Statue\Command\SetStatuePositionCommand;

use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;

use JsonException;

final class Statue extends PluginBase
{
    public const PREFIX = '§l§a[제단] §r§7';

    private static Config $config;
    public static array $datas;

    public static array $statue_items = [
        // '1:1' => 0.1 // 1:1 아이템이 게이지 0.1% 채움
           '133:0' => 2.0,
           '57:0' => 5.0,
           '152:0' => 0.1,
           '173:0' => 0.1,
           '41:0' => 1.0,
           '42:0' => 1.0,
           '22:0' => 0.1,
           '7:0' => 100,
    ];

    protected function onEnable() : void
    {
        self::$config = new Config($this->getDataFolder().'data.yml',Config::YAML,[
            'STATUE_POSITION' => [],
            'STATUE_REWARDS' => [],
            'STATUE_GUAGE' => 0,
            'STATUE_STATE' => true
        ]);
        self::$datas = self::$config->getAll();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this->getScheduler()),$this);
        $this->getServer()->getCommandMap()->registerAll($this->getName(),[
            new SetStatueRewardsCommand(),
            new SetStatuePositionCommand(),
            new StatueDisableCommand()
        ]);
        CreativeInventory::getInstance()->add(StatueUtil::getStateEditTool());
    }

    /** @throws JsonException */
    protected function onDisable() : void
    {
        self::$config->setAll(self::$datas);
        self::$config->save();
    }
}
