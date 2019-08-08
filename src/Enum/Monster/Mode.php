<?php
namespace Cydh\DP2RA\Enum\Monster;

class Mode
{
    const MD_NONE               = 0x0000000;
    const MD_CANMOVE            = 0x0000001;
    const MD_LOOTER             = 0x0000002;
    const MD_AGGRESSIVE         = 0x0000004;
    const MD_ASSIST             = 0x0000008;
    const MD_CASTSENSOR_IDLE    = 0x0000010;
    const MD_NORANDOM_WALK      = 0x0000020;
    const MD_NOCAST_SKILL       = 0x0000040;
    const MD_CANATTACK          = 0x0000080;
    const MD_CASTSENSOR_CHASE   = 0x0000200;
    const MD_CHANGECHASE        = 0x0000400;
    const MD_ANGRY              = 0x0000800;
    const MD_CHANGETARGET_MELEE = 0x0001000;
    const MD_CHANGETARGET_CHASE = 0x0002000;
    const MD_TARGETWEAK         = 0x0004000;
    const MD_RANDOMTARGET       = 0x0008000;
    const MD_IGNOREMELEE        = 0x0010000;
    const MD_IGNOREMAGIC        = 0x0020000;
    const MD_IGNORERANGED       = 0x0040000;
    const MD_MVP                = 0x0080000;
    const MD_IGNOREMISC         = 0x0100000;
    const MD_KNOCKBACK_IMMUNE   = 0x0200000;
    const MD_TELEPORT_BLOCK     = 0x0400000;
    const MD_FIXED_ITEMDROP     = 0x1000000;
    const MD_DETECTOR           = 0x2000000;
    const MD_STATUS_IMMUNE      = 0x4000000;
    const MD_SKILL_IMMUNE       = 0x8000000;
}
