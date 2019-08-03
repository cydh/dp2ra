<?php
namespace Cydh\DP2RA\Enum;

class Location
{
	const HEAD_TOP = 256; // Upper Headgear
	const HEAD_MID = 512; // Middle Headgear
	const HEAD_LOW = 001; // Lower Headgear
	const ARMOR = 16; // Armor
	const WEAPON = 2; // Weapon / Right-hand
	const SHIELD = 32; // Shield / Left-hand
	const GARMENT = 4; // Garment
	const FOOTGEAR = 64; // Footgear
	const ACC_LEFT = 8; // Accessory Right
	const ACC_RIGHT = 128; // Accessory Left
	const COSTUME_HEAD_TOP= 1024; // Costume Top Headgear
	const COSTUME_HEAD_MID = 2048; // Costume Mid Headgear
	const COSTUME_HEAD_LOW = 4096; // Costume Low Headgear
	const COSTUME_GARMENT = 8192; // Costume Garment/Robe
	const AMMO = 32768; // Ammo
	const SHADOW_ARMOR = 65536; // Shadow Armor
	const SHADOW_WEAPON = 131072; // Shadow Weapon
	const SHADOW_SHIELD = 262144; // Shadow Shield
	const SHADOW_SHOES = 524288; // Shadow Shoes
	const SHADOW_ACC_RIGHT = 1048576; // Shadow Accessory Right (Earring)
	const SHADOW_ACC_LEFT = 2097152; // Shadow Accessory Left (Pendant)
}
