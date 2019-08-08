<?php
namespace Cydh\DP2RA\Enum\Monster;

use Cydh\DP2RA\Enum\Monster\Mode;

class AegisAttr
{
    static public function toMode($attr)
    {
        if (!$attr) {
            return Mode::MD_NONE;
        }
        $mode = Mode::MD_NONE;

        // 0x010000 (takes 1 damage from melee attacks)
        if ($attr&0x01) {
            $mode |= Mode::MD_IGNOREMELEE;
        }
        // 0x020000 (takes 1 damage from magic attacks)
        if ($attr&0x02) {
            $mode |= Mode::MD_IGNOREMAGIC;
        }
        // 0x040000 (takes 1 damage from ranged attacks)
        if ($attr&0x04) {
            $mode |= Mode::MD_IGNORERANGED;
        }
        // 0x100000 (takes 1 damage from misc attacks)
        if ($attr&0x10) {
            $mode |= Mode::MD_IGNOREMISC;
        }
        // 0x200000 (cannot be knocked back)
        if ($attr&0x20) {
            $mode |= Mode::MD_KNOCKBACK_IMMUNE;
        }
        // 0x400000 (teleport block)
        if ($attr&0x40) {
            $mode |= Mode::MD_TELEPORT_BLOCK;
        }
        return $mode;
    }
}
