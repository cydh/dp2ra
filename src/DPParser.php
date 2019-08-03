<?php
namespace Cydh\DP2RA;

use Cydh\DP2RA\Enum;
use Cydh\DP2RA\Location;
use Cydh\DP2RA\View;

class DPParser
{
    static public function clearName($name)
    {
        $name = preg_replace('/([^[]+)\[\d+\].*/', "$1", $name);
        return trim($name);
    }

    static public function itemTypeLoc($type, $subtype, $acc, $compos)
    {
        $type_ = null;
        $loc_ = null;
        $view_ = null;

        switch ((int)$type) {
            case 1:
                $type_ = Enum\Type::IT_WEAPON;
                break;
            case 2:
                $type_ = Enum\Type::IT_ARMOR;
                // same type with pet eq & equip
                // pet eq has same type and subtype with pet equip
                $loc_ = DPParser::parseSubTypeIDArmor((int)$subtype);
                break;
            case 3:
                $type_ = Enum\Type::IT_HEALING;
                break;
            case 4:
                $type_ = Enum\Type::IT_AMMO;
                break;
            case 6:
                $type_ = Enum\Type::IT_CARD;
                break;
            case 9:
                $type_ = Enum\Type::IT_SHADOW_EQUIPMENT;
                $loc_ = Enum\Location::SHADOW_WEAPON;
                break;
            case 10:
                $type_ = Enum\Type::IT_SHADOW_EQUIPMENT;
                $loc_ = DPParser::parseSubTypeIDShadow((int)$subtype);
                break;
            case 5:
            default:
                $type_ = Enum\Type::IT_ETC;
                break;
        }

        DPParser::parseSubTypeID((int)$subtype, $loc_, $view_);
        if ($loc_ == Enum\Location::SHADOW_WEAPON) {
            $type_ = Enum\Type::IT_SHADOW_EQUIPMENT;
        }

        return [
            'type' => $type_,
            'loc' => $loc_,
            'view' => $view_,
        ];
    }

    static public function parseSubTypeIDArmor($subtype)
    {
        switch ($subtype) {
            case 512: return Enum\Location::HEAD_TOP; // all HG from Divine-pride is 512
            case 513: return Enum\Location::ARMOR;
            case 514: return Enum\Location::SHIELD;
            case 515: return Enum\Location::GARMENT;
            case 516: return Enum\Location::FOOTGEAR;
            case 517: return Enum\Location::ACC_LEFT|Enum\Location::ACC_RIGHT;
        }
        return null;
    }

    static public function parseSubTypeIDShadow($subtype)
    {
        switch ($subtype) {
            case 526: return Enum\Location::SHADOW_ARMOR; // type 10
            case 280: return Enum\Location::SHADOW_WEAPON; // type 1
            case 527: return Enum\Location::SHADOW_SHIELD; // type 10
            case 528: return Enum\Location::SHADOW_SHOES; // type 10
            case 529: return Enum\Location::SHADOW_ACC_RIGHT; // type 10
            case 530: return Enum\Location::SHADOW_ACC_LEFT; // type 10
        }
        return null;
    }

    static public function parseSubTypeID($subtype, &$loc, &$view)
    {
        if (($w_idx = ($subtype & 0xFF))) { // 255
            $info = [
                [
                    'loc' => Enum\Location::WEAPON,
                    'view' => Enum\View::WEAPON_HAND,
                ],
                [
                    'loc' => Enum\Location::WEAPON,
                    'view' => Enum\View::WEAPON_DAGGER,
                ],
                [
                    'loc' => Enum\Location::WEAPON,
                    'view' => Enum\View::WEAPON_ONEHAND_SWORD,
                ],
                [
                    'loc' => Enum\Location::WEAPON|Enum\Location::SHIELD,
                    'view' => Enum\View::WEAPON_TWOHAND_SWORD,
                ],
                [
                    'loc' => Enum\Location::WEAPON,
                    'view' => Enum\View::WEAPON_ONEHAND_SPEAR,
                ],
                [
                    'loc' => Enum\Location::WEAPON|Enum\Location::SHIELD,
                    'view' => Enum\View::WEAPON_TWOHAND_SPEAR,
                ],
                [
                    'loc' => Enum\Location::WEAPON,
                    'view' => Enum\View::WEAPON_ONEHAND_AXE,
                ],
                [
                    'loc' => Enum\Location::WEAPON|Enum\Location::SHIELD,
                    'view' => Enum\View::WEAPON_TWOHAND_AXE,
                ],
                [
                    'loc' => Enum\Location::WEAPON,
                    'view' => Enum\View::WEAPON_MACE,
                ],
                [
                    'loc' => null,
                    'view' => null,
                ],
                [
                    'loc' => Enum\Location::WEAPON,
                    'view' => Enum\View::WEAPON_STAFF,
                ],
                [
                    'loc' => Enum\Location::WEAPON|Enum\Location::SHIELD,
                    'view' => Enum\View::WEAPON_BOW,
                ],
                [
                    'loc' => Enum\Location::WEAPON,
                    'view' => Enum\View::WEAPON_KNUCKLE,
                ],
                [
                    'loc' => Enum\Location::WEAPON,
                    'view' => Enum\View::WEAPON_INSTRUMENT,
                ],
                [
                    'loc' => Enum\Location::WEAPON,
                    'view' => Enum\View::WEAPON_WHIP,
                ],
                [
                    'loc' => Enum\Location::WEAPON,
                    'view' => Enum\View::WEAPON_BOOK,
                ],
                [
                    'loc' => Enum\Location::WEAPON|Enum\Location::SHIELD,
                    'view' => Enum\View::WEAPON_KATAR,
                ],
                [
                    'loc' => Enum\Location::WEAPON|Enum\Location::SHIELD,
                    'view' => Enum\View::WEAPON_REVOLVER,
                ],
                [
                    'loc' => Enum\Location::WEAPON|Enum\Location::SHIELD,
                    'view' => Enum\View::WEAPON_RIFLE,
                ],
                [
                    'loc' => Enum\Location::WEAPON|Enum\Location::SHIELD,
                    'view' => Enum\View::WEAPON_GATLING,
                ],
                [
                    'loc' => Enum\Location::WEAPON|Enum\Location::SHIELD,
                    'view' => Enum\View::WEAPON_SHOTGUN,
                ],
                [
                    'loc' => Enum\Location::WEAPON|Enum\Location::SHIELD,
                    'view' => Enum\View::WEAPON_GRENADE_LAUNCHER,
                ],
                [
                    'loc' => Enum\Location::WEAPON|Enum\Location::SHIELD,
                    'view' => Enum\View::WEAPON_FUUMA_SHURIKEN,
                ],
                [
                    'loc' => Enum\Location::WEAPON|Enum\Location::SHIELD,
                    'view' => Enum\View::WEAPON_TWOHANDED_STAFF,
                ],
                [
                    'loc' => null,
                    'view' => null,
                ],
                [
                    'loc' => Enum\Location::SHADOW_WEAPON,
                    'view' => null,
                ],
            ];
            if (array_key_exists($w_idx, $info)) {
                $loc = $info[$w_idx]['loc'];
                $view = $info[$w_idx]['view'];
            }
        }

        if ($subtype & 0x400) {
            switch ($subtype) {
                case 1024:
                    $view = Enum\View::AMMO_ARROW;
                    break;
                case 1025:
                    $view = Enum\View::AMMO_CANNONBALL;
                    break;
                case 1026:
                    $view = Enum\View::AMMO_SLING;
                    break;
                case 1027:
                    $view = Enum\View::AMMO_BULLET;
                    break;
            }
            // $info = [
            //     Enum\View::AMMO_ARROW, // 1024
            //     Enum\View::AMMO_DAGGER, // 1024
            //     Enum\View::AMMO_BULLET, // 1027
            //     Enum\View::AMMO_SHELL, // 1027
            //     Enum\View::AMMO_GRENADE, // 1027
            //     Enum\View::AMMO_SHURIKEN, // 1026
            //     Enum\View::AMMO_KUNAI, // 1026
            //     Enum\View::AMMO_CANNONBALL, // 1025
            //     Enum\View::AMMO_SLING, // 1026
            // ];
            $loc = Enum\Location::AMMO;
        }
    }

    static public function itemLoc($location, $accessory, $composition_pos)
    {
        // TODO
        return "";
    }

    static public function itemJob($job)
    {
        // TODO
        if (empty($job)) {
            return [
                "job" => null,
                "class" => null,
            ];
        }

        $job_ra = "0xFFFFFFFF";
        $class_ra = 63;

        return [
            "job" => $job_ra,
            "class" => $class_ra,
        ];
    }

    static public function tradeFlag($move_flag)
    {
        $flag = 0;
        if ($move_flag["drop"])
            $flag |= 1;
        if ($move_flag["trade"])
            $flag |= 2;
        if ($move_flag["sell"])
            $flag |= 8;
        if ($move_flag["cart"])
            $flag |= 16;
        if ($move_flag["store"])
            $flag |= 32;
        if ($move_flag["guildStore"])
            $flag |= 64;
        if ($move_flag["mail"])
            $flag |= 128;
        if ($move_flag["auction"])
            $flag |= 256;
        return $flag;
    }

}
