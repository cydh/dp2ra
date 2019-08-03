<?php
namespace Cydh\DP2RA\Enum;

class Job
{
	const NOVICE         = 0x00000001;
	const SWORDMAN       = 0x00000002;
	const MAGICIAN       = 0x00000004;
	const ARCHER         = 0x00000008;
	const ACOLYTE        = 0x00000010;
	const MERCHANT       = 0x00000020;
	const THIEF          = 0x00000040;
	const KNIGHT         = 0x00000080;
	const PRIEST         = 0x00000100;
	const WIZARD         = 0x00000200;
	const BLACKSMITH     = 0x00000400;
	const HUNTER         = 0x00000800;
	const ASSASSIN       = 0x00001000;
	//const UNUSED         = 0x00002000;
	const CRUSADER       = 0x00004000;
	const MONK           = 0x00008000;
	const SAGE           = 0x00010000;
	const ROGUE          = 0x00020000;
	const ALCHEMIST      = 0x00040000;
	const BARD_DANCER    = 0x00080000;
	//const UNUSED         = 0x00100000;
	const TAEKWON        = 0x00200000;
	const STAR_GLADIATOR = 0x00400000;
	const SOUL_LINKER    = 0x00800000;
	const GUNSLINGER     = 0x01000000;
	const NINJA          = 0x02000000;
	const GANGSI         = 0x04000000;
	const DEATH_KNIGHT   = 0x08000000;
	const DARK_COLLECTOR = 0x10000000;
	const KAGEROU_OBORO  = 0x20000000;
	const REBELLION      = 0x40000000;
	const SUMMONER       = 0x80000000;
	const ALL_JOBS       = 0xFFFFFFFF;

	const ALL_CLASSES = 63;
}
