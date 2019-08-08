<?php
/*
Read Item list from DP's json file.
The file must equivalent to (array as example):

$items = [ // array of item list
    "5909" => [ // item id as key
        "aegisName" => "C_Valkyrie_Circlet",
        "name" => "의상 발키리의 서클릿",
        "slots" => 0,
        "itemTypeId" => 2,
        "itemSubTypeId" => 512,
        "attack" => 0,
        "defense" => 0,
        "weight" => 0,
        "requiredLevel" => null,
        "limitLevel" => 0,
        "weaponLevel" => 0,
        "job" => null,
        "compositionPos" => null,
        "attribute" => 0,
        "location" => null,
        "accessory" => "ACCESSORY_VALKYRIE_CIRCLET",
        "price" => 0,
        "range" => null,
        "matk" => null,
        "gender" => 2,
        "refinable" => false,
        "indestructible" => false,
        "itemMoveInfo" => [
            "drop" => true,
            "trade" => true,
            "store" => true,
            "cart" => true,
            "sell" => true,
            "mail" => true,
            "auction" => true,
            "guildStore" => true
        ],
    ],
    // another item
];

*/
namespace Cydh\DP2RA;

class Items
{
    private $input_file = "";
    private $items_raw = [];
    private $out_itemdb_file = "";
    private $out_itemdb_fp;
    private $out_moveinfo_file = "";
    private $out_moveinfo_fp;
    private $items_total = 0;
    private $items_done = 0;
    private $id; // current item id
    private $data; // current input item
    private $item; // current parsed item

    public $items_db = [];
    public $items_trade_db = [];

    public function __construct()
    {
        print "Preparing magic circle...".PHP_EOL;
    }

    private function parseItem($write_output = true)
    {
        // ID,AegisName,Name,Type,Buy,Sell,Weight,ATK[:MATK],DEF,Range,Slots,Job,Class,Gender,Loc,wLV,eLV[:maxLevel],Refineable,View,{ Script },{ OnEquip_Script },{ OnUnequip_Script }
        $this->item['id'] = $this->id;
        $this->item['aegisName'] = str_replace("'", "", DPParser::clearName($this->data["aegisName"]));
        $this->item['name'] = DPParser::clearName($this->data["name"]);
        $this->item['type'] = null;
        $this->item['price_buy'] = $this->data["price"];
        $this->item['price_sell'] = null;
        $this->item['weight'] = $this->data["weight"];

        $this->item['attack'] = $this->parseAttack();

        $this->item['defense'] = $this->data["defense"];
        $this->item['range'] = $this->data["range"];
        $this->item['slots'] = $this->data["slots"];

        $this->item['job'] = null;
        $this->item['class'] = null;
        $this->parseJobClass();

        $this->item['gender'] = $this->data["gender"];
        $this->item['loc'] = null;

        $this->item['weaponLevel'] = $this->data["weaponLevel"];
        $this->item['req_lv'] = $this->parseItemReqLevel();
        $this->item['refinable'] = $this->data["refinable"];
        $this->item['view'] = null;
        $this->parseItemTypeLoc();

        //$this->item['indestructible'] = '{ bonus bUn ..'.$this->data["indestructible"].'; }';
        //if ($this->data["attribute"])
        //    $this->item['script1'] = '{ bonus2 bAtkEle,Ele_what; }';
        //else
            $this->item['script1'] = '{}';
        $this->item['script2'] = '{}';
        $this->item['script3'] = '{}';

        ++$this->items_done;
        $trade_flag = $this->parseTradeFlag();

        if ($write_output) {
            fputs($this->out_itemdb_fp, implode(",", array_values($this->item))."\r\n");
            fputs($this->out_moveinfo_fp, "".$this->id.",".$trade_flag."\t// ".$this->data["name"]."\r\n");
        } else {
            $this->items_db[$this->id] = $this->item;
            $this->items_trade_db[$this->id] = [
                'name' => $this->data["name"],
                'trade' => $trade_flag,
            ];
        }
    }

    private function parseItemTypeLoc()
    {
        $type_loc = DPParser::itemTypeLoc($this->data["itemTypeId"], $this->data["itemSubTypeId"], $this->data["accessory"], $this->data["compositionPos"]);
        $this->item['type'] = $type_loc['type'];
        $this->item['loc'] = $type_loc['loc'];
        $this->item['view'] = $type_loc['view'];

        if ($this->item['loc'] != null && $this->item['job'] == null) {
            $this->item['job'] = "0xFFFFFFFF";
            $this->item['class'] = "63";
            $this->item['gender'] = "2";
        }
    }

    private function parseAttack()
    {
        $attack = null;

        if ($this->data["attack"] && $this->data["matk"])
            $attack = $this->data["attack"].":".$this->data["matk"];
        elseif (!$this->data["attack"] && $this->data["matk"])
            $attack = "0:".$this->data["matk"];
        elseif ($this->data["attack"] && !$this->data["matk"])
            $attack = "0";

        return $attack;
    }

    private function parseJobClass()
    {
        $job = DPParser::itemJob($this->data["job"]);
        $this->item['job'] = $job["job"];
        $this->item['class'] = $job["class"];
    }

    private function parseLoc()
    {
        return DPParser::itemLoc($this->data["location"], $this->data["accessory"], $this->data["compositionPos"]);
    }

    private function parseItemReqLevel()
    {
        $req_lv = null;

        if ($this->data["requiredLevel"] && $this->data["limitLevel"])
            $req_lv = $this->data["requiredLevel"].":".$this->data["limitLevel"];
        elseif (!$this->data["requiredLevel"] && $this->data["limitLevel"])
            $req_lv = "0:".$this->data["limitLevel"];
        elseif ($this->data["requiredLevel"] && !$this->data["limitLevel"])
            $req_lv = "0";

        return $req_lv;
    }

    private function parseTradeFlag()
    {
        if (isset($this->data["itemMoveInfo"]) && $this->data["itemMoveInfo"] !== null) {
            return DPParser::tradeFlag($this->data["itemMoveInfo"]);
        }
        return null;
    }

    public function parseInputFile($filename)
    {
        $this->input_file = isset($filename) ? $filename : "item_db.json";

        $file = file_get_contents($this->input_file);
        if (!$file) {
            print "Cannot read file ".$this->input_file.PHP_EOL;
            return;
        }
        print "Material added ".$this->input_file.PHP_EOL;

        $this->items_raw = json_decode($file, true);
        if (!$this->items_raw || !is_array($this->items_raw) || !($this->items_total = count($this->items_raw))) {
            print "Invalid entries of ".$this->input_file.PHP_EOL;
            return;
        }
        print "Material quality: Medium".PHP_EOL;
    }

    public function setOutputItemDB($filename)
    {
        $this->out_itemdb_file = isset($filename) ? $filename : "item_db.txt";

        $this->out_itemdb_fp = fopen($this->out_itemdb_file, "w+");
        if (!$this->out_itemdb_fp) {
            print "Cannot write output file ".$this->out_itemdb_file.PHP_EOL;
            return;
        }
        print "Vessel created at ".$this->out_itemdb_file.PHP_EOL;
    }

    public function setOutputTradeDB($filename)
    {
        $this->out_moveinfo_file = isset($filename) ? $filename : "item_trade.txt";

        $this->out_moveinfo_fp = fopen($this->out_moveinfo_file, "w+");
        if (!$this->out_moveinfo_fp) {
            print "Cannot write output file ".$this->out_moveinfo_file.PHP_EOL;
            return;
        }
        print "Secondary vessel was prepared at ".$this->out_itemdb_file.PHP_EOL;
    }

    public function parseData($write_output = true)
    {
        print "Chanting the strongest magick!!".PHP_EOL;

        $this->items_done = 0;
        ksort($this->items_raw);
        foreach ($this->items_raw as $id => $data) {
            $this->id = $id;
            $this->data = $data;
            $this->parseItem($write_output);
        }
    }

    public function writeParsed()
    {
        foreach ($this->items_db as $item) {
            fputs($this->out_itemdb_fp, implode(",", array_values($item))."\r\n");
        }
        foreach ($this->items_trade_db as $id => $trade) {
            fputs($this->out_moveinfo_fp, "".$id.",".$trade['trade']."\t// ".$trade["name"]."\r\n");
        }
    }

    static public function parse(array $params = [ 'input' => null, 'output_itemdb' => null, 'output_tradedb' => null ])
    {
        $instance = new self();
        $instance->parseInputFile($params['input']);

        $instance->setOutputItemDB($params['output_itemdb']);
        $instance->setOutputTradeDB($params['output_tradedb']);

        $instance->parseData();
    }

    public function __destruct()
    {
        print "Done destroying ".$this->items_done." of ".$this->items_total." mortal objects!!".PHP_EOL;
        if ($this->out_itemdb_fp)
            fclose($this->out_itemdb_fp);
        if ($this->out_moveinfo_fp)
            fclose($this->out_moveinfo_fp);
        print "Magick done.".PHP_EOL;
    }
}
