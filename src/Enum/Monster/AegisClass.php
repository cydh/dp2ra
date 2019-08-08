<?php
namespace Cydh\DP2RA\Enum\Monster;

use Cydh\DP2RA\Enum\Monster\Mode;

class AegisClass
{
    static public function toMode($class)
    {
        if (!$class) {
            return Mode::MD_NONE;
        }
        switch ((int)$class) {
            case 0:
            // 0x0000000 normal monster
            return Mode::MD_NONE;
            case 1:
            // 0x6200000 boss class, immune to status changes, immune to knockback, detector
            return
            Mode::MD_STATUS_IMMUNE|
            Mode::MD_KNOCKBACK_IMMUNE|
            Mode::MD_DETECTOR;
            case 2:
            // 0x4000000 guardian class, immune to status changes
            return
            Mode::MD_STATUS_IMMUNE;
            case 4:
            // 0xC000000 battlefield class, immune to status changes, completely ignores all skills
            return
            Mode::MD_STATUS_IMMUNE|
            Mode::MD_SKILL_IMMUNE;
            case 5:
            // 0x1000000 event class, ignores all drop rate adjustments
            return
            Mode::MD_FIXED_ITEMDROP;
        }
    }
}
