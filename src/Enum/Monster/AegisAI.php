<?php
namespace Cydh\DP2RA\Enum\Monster;

use Cydh\DP2RA\Enum\Monster\Mode;

class AegisAI
{
    static public function toMode($ai)
    {
        if (!$ai) {
            return Mode::MD_NONE; // 0x0000000 normal monster
        }
        $pos = strlen("MONSTER_TYPE_");
        $num = substr($ai, $pos, strlen($ai) - $pos);
        switch ((int)$num) {
            case 1: return 0x0081; // (passive)
            case 2: return 0x0083; // (passive, looter)
            case 3: return 0x1089; // (passive, assist and change-target melee)
            case 4: return 0x3885; // (angry, change-target melee/chase)
            case 5: return 0x2085; // (aggressive, change-target chase)
            case 6: return 0x0000; // (passive, immobile, can't attack) [plants]
            case 7: return 0x108B; // (passive, looter, assist, change-target melee)
            case 8: return 0x7085; // (aggressive, change-target melee/chase, target weak enemies)
            case 9: return 0x3095; // (aggressive, change-target melee/chase, cast sensor idle) [Guardian]
            case 10: return 0x0084; // (aggressive, immobile)
            case 11: return 0x0084; // (aggressive, immobile) [Guardian]
            case 12: return 0x2085; // (aggressive, change-target chase) [Guardian]
            case 13: return 0x308D; // (aggressive, change-target melee/chase, assist)
            case 17: return 0x0091; // (passive, cast sensor idle)
            case 19: return 0x3095; // (aggressive, change-target melee/chase, cast sensor idle)
            case 20: return 0x3295; // (aggressive, change-target melee/chase, cast sensor idle/chase)
            case 21: return 0x3695; // (aggressive, change-target melee/chase, cast sensor idle/chase, chase-change target)
            case 24: return 0x00A1; // (passive, does not walk randomly) [Slave]
            case 25: return 0x0001; // (passive, can't attack) [Pet]
            case 26: return 0xB695; // (aggressive, change-target melee/chase, cast sensor idle/chase, chase-change target, random target)
            case 27: return 0x8084; // (aggressive, immobile, random target)
        }
    }
}
