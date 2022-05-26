<?php

declare(strict_types=1);

namespace DOHWI\Statue\Util;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

final class StatueUtil
{
    public static function getStateEditTool() : Item
    {
        $item = VanillaItems::STONE_AXE();
        $item->setCustomName('§r§f제단 범위 설정§r');
        $item->setLore(["§r","§f- 구역 터치: §a1번§r","§f- 구역 파괴: §a2번§r"]);
        $item->getNamedTag()->setByte('STATUE_TOOL',1);
        return $item;
    }
}
