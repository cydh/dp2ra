<?php
/*
Read Monsters list from DP's json file
*/
namespace Cydh\DP2RA;

class Monsters
{
    private $input_file = "";
    private $monsters_raw = [];
    private $out_mobdb_file = "";
    private $out_mobdb_fp;
    private $out_skill_file = "";
    private $out_skill_fp;
    private $out_spawn_file = "";
    private $out_spawn_fp;
    private $num_total = 0;
    private $num_done = 0;
    private $current_id;
    private $current_data;
    private $entry; // current parsed item
    private $db_structure = [];

    public $mob_db = [];
    public $mob_skill_db = [];
    public $mob_spawn = [];

    public function __construct()
    {
        print "Preparing magic circle...".PHP_EOL;
        $this->db_structure = [
            "ID", "Sprite_Name", "kROName", "iROName", "LV", "HP", "SP", "EXP", "JEXP", "Range1",
            "ATK1", "ATK2", "DEF", "MDEF", "STR", "AGI", "VIT", "INT", "DEX", "LUK", "Range2", "Range3",
            "Scale", "Race", "Element", "Mode", "Speed", "aDelay", "aMotion", "dMotion",
            "MEXP", "MVP1id", "MVP1per", "MVP2id", "MVP2per", "MVP3id", "MVP3per",
            "Drop1id", "Drop1per", "Drop2id", "Drop2per", "Drop3id", "Drop3per", "Drop4id", "Drop4per",
            "Drop5id", "Drop5per", "Drop6id", "Drop6per", "Drop7id", "Drop7per", "Drop8id", "Drop8per",
            "Drop9id", "Drop9per",
            "DropCardid", "DropCardper",
        ];
    }

    private function parseItem($write_output = true)
    {
        $this->entry = array_combine($this->db_structure, array_fill(0, count($this->db_structure), null));
        // ID,Sprite_Name,kROName,iROName,LV,HP,SP,EXP,JEXP,Range1,ATK1,ATK2,DEF,MDEF,STR,AGI,VIT,INT,DEX,LUK,Range2,Range3,Scale,Race,Element,Mode,Speed,aDelay,aMotion,dMotion,MEXP,MVP1id,MVP1per,MVP2id,MVP2per,MVP3id,MVP3per,Drop1id,Drop1per,Drop2id,Drop2per,Drop3id,Drop3per,Drop4id,Drop4per,Drop5id,Drop5per,Drop6id,Drop6per,Drop7id,Drop7per,Drop8id,Drop8per,Drop9id,Drop9per,DropCardid,DropCardper
        $this->entry['ID'] = $this->current_id;
        $this->entry['Sprite_Name'] = str_replace("'", "", DPParser::clearName($this->current_data["dbname"]));
        $this->entry['kROName'] = str_replace("'", "", DPParser::clearName(isset($this->current_data["name"]) ? $this->current_data["name"] : $this->current_data["dbname"]));
        $this->entry['iROName'] = $this->entry['kROName'];

        $stats = &$this->current_data["stats"];
        $this->entry['LV'] = $stats["level"];
        $this->entry['HP'] = $stats["health"];
        $this->entry['SP'] = 0;
        $this->entry['EXP'] = $stats["baseExperience"];
        $this->entry['JEXP'] = $stats["jobExperience"];
        $this->entry['Range1'] = $stats["attackRange"];
        $this->entry['ATK1'] = isset($stats["attack"], $stats["attack"]["minimum"]) ? $stats["attack"]["minimum"] : null;
        $this->entry['ATK2'] = isset($stats["attack"], $stats["attack"]["maximum"]) ? $stats["attack"]["maximum"] : null;
        $this->entry['DEF'] = $stats["defense"];
        $this->entry['MDEF'] = $stats["magicDefense"];
        $this->entry['STR'] = $stats["str"];
        $this->entry['AGI'] = $stats["agi"];
        $this->entry['VIT'] = $stats["vit"];
        $this->entry['INT'] = $stats["int"];
        $this->entry['DEX'] = $stats["dex"];
        $this->entry['LUK'] = $stats["luk"];
        $this->entry['Range2'] = $stats["aggroRange"];
        $this->entry['Range3'] = $stats["escapeRange"];
        $this->entry['Scale'] = $stats["scale"];
        $this->entry['Race'] = $stats["race"];
        $this->entry['Element'] = $stats["element"];
        $this->entry['Mode'] = DPParser::monsterMode($stats["class"], $stats["ai"], $stats["attr"]);
        if ($stats["mvp"]) {
            $this->entry['Mode'] |= Enum\Monster\Mode::MD_MVP;
        }
        $this->entry['Mode'] = sprintf("0x%07X", $this->entry['Mode']);
        $this->entry['Speed'] = (int)$stats["movementSpeed"];
        $this->entry['aDelay'] = (int)$stats["rechargeTime"];
        $this->entry['aMotion'] = (int)$stats["attackSpeed"];
        $this->entry['dMotion'] = (int)$stats["attackedSpeed"];

        $this->entry['MEXP']  = (int)($stats["mvp"] ? $stats["baseExperience"]/2 : 0);
        $i = 1;
        foreach($this->current_data["mvpdrops"] as &$item) {
            $this->entry['MVP'.$i.'id'] = $item["itemId"];
            $this->entry['MVP'.$i.'per'] = $item["chance"];
            ++$i;
        }
        $i = 1;
        foreach($this->current_data["drops"] as &$item) {
            $itemId = (int)$item["itemId"];
            if ($itemId > 4000 && $itemId < 4700) {
                $this->entry['DropCardid'] = $itemId;
                $this->entry['DropCardid']= $item["chance"];
            }
            else {
                if ($itemId == 512 && !$item["chance"]) {
                    $itemId = null;
                    $chance = null;
                }
                else {
                    $chance = ($item["chance"] == 0 ? 1 : 0);
                }
                $this->entry['Drop'.$i.'id'] = $itemId;
                $this->entry['Drop'.$i.'per'] = $chance;
                ++$i;
            }
        }

        $skills = DPParser::monsterSkills(
            $this->current_data["dbname"],
            $this->current_id,
            $this->current_data["skill"]
        );
        $spawn = DPParser::monsterSpawn(
            $stats["mvp"],
            $this->current_data["dbname"],
            $this->current_id,
            $this->current_data["spawn"]
        );

        if ($write_output) {
            fputs($this->out_mobdb_fp, implode(",", array_values($this->entry))."\r\n");
            fputs($this->out_skill_fp, "".$this->current_id.",".$trade_flag."\t// ".$this->current_data["name"]."\r\n");
            fputs($this->out_skill_fp, "".$this->current_id.",".$trade_flag."\t// ".$this->current_data["name"]."\r\n");
            foreach ($skills as &$skill)
                fputs($this->out_skill_fp, Monsters::printSpawn($skill)."\r\n");
            foreach ($spawn as &$s)
                fputs($this->out_spawn_fp, implode(",", array_values($s))."\r\n");
        } else {
            $this->mob_db[$this->current_id] = $this->entry;
            if (!empty($skills))
                $this->mob_skill_db[$this->current_id] = $skills;
            if (!empty($spawn))
                $this->mob_spawn[$this->current_id] = $spawn;
        }

        ++$this->num_done;
    }

    public function parseInputFile($filename)
    {
        $this->input_file = isset($filename) ? $filename : "mob_db.json";

        $file = file_get_contents($this->input_file);
        if (!$file) {
            print "Cannot read file ".$this->input_file.PHP_EOL;
            return;
        }
        print "Material added ".$this->input_file.PHP_EOL;

        $this->monsters_raw = json_decode($file, true);
        if (!$this->monsters_raw || !is_array($this->monsters_raw) || !($this->num_total = count($this->monsters_raw))) {
            print "Invalid entries of ".$this->input_file.PHP_EOL;
            return;
        }
        print "Material quality: Medium".PHP_EOL;
    }

    public function setOutputMonsterDB($filename)
    {
        $this->out_mobdb_file = isset($filename) ? $filename : "mob_db.txt";

        $this->out_mobdb_fp = fopen($this->out_mobdb_file, "w+");
        if (!$this->out_mobdb_fp) {
            print "Cannot write output file ".$this->out_mobdb_file.PHP_EOL;
            return;
        }
        print "Vessel created at ".$this->out_mobdb_file.PHP_EOL;
    }

    public function setOutputMonsterSkillDB($filename)
    {
        $this->out_skill_file = isset($filename) ? $filename : "mob_skill_db.txt";

        $this->out_skill_fp = fopen($this->out_skill_file, "w+");
        if (!$this->out_skill_fp) {
            print "Cannot write output file ".$this->out_skill_file.PHP_EOL;
            return;
        }
        print "Secondary vessel was prepared at ".$this->out_skill_file.PHP_EOL;
    }

    public function setOutputMonsterSpawnDB($filename)
    {
        $this->out_spawn_file = isset($filename) ? $filename : "spawn.txt";

        $this->out_spawn_fp = fopen($this->out_spawn_file, "w+");
        if (!$this->out_spawn_fp) {
            print "Cannot write output file ".$this->out_spawn_file.PHP_EOL;
            return;
        }
        print "Ternary vessel was prepared at ".$this->out_spawn_file.PHP_EOL;
    }

    public function parseData($write_output = true)
    {
        print "Chanting the strongest magick!!".PHP_EOL;

        $this->num_done = 0;
        ksort($this->monsters_raw);
        foreach ($this->monsters_raw as $id => $data) {
            $this->current_id = $id;
            $this->current_data = $data;
            $this->parseItem($write_output);
        }
    }

    public function writeParsed()
    {
        foreach ($this->mob_db as &$item) {
            fputs($this->out_mobdb_fp, implode(",", array_values($item))."\r\n");
        }
        foreach ($this->mob_skill_db as $id => $skills) {
            foreach ($skills as &$skill) {
                fputs($this->out_skill_fp, implode(",", array_values($skill))."\r\n");
            }
        }
        foreach ($this->mob_spawn as $id => $spawns) {
            foreach ($spawns as &$spawn) {
                fputs($this->out_spawn_fp, Monsters::printSpawn($spawn)."\r\n");
            }
        }
    }

    static public function printSpawn(array $spawn = [])
    {
        $s = $spawn['map'];
        $s .= ",".$spawn['x'];
        $s .= ",".$spawn['y'];
        $s .= ",".$spawn['xs'];
        $s .= ",".$spawn['ys'];
        $s .= "\t".$spawn['type'];
        $s .= "\t".$spawn['name'];
        $s .= "\t".$spawn['mobid'];
        $s .= ",".$spawn['amount'];
        $s .= ",".$spawn['delay1'];
        $s .= ",".$spawn['delay2'];
        return $s;
    }

    static public function parse(array $params = [ 'input' => null, 'output_mobdb' => null, 'output_mobskilldb' => null, 'output_spawn' => null ])
    {
        $instance = new self();
        $instance->parseInputFile($params['input']);

        $instance->setOutputMonsterDB($params['output_mobdb']);
        $instance->setOutputMonsterSkillDB($params['output_mobskilldb']);
        $instance->setOutputMonsterSpawnDB($params['output_spawn']);

        $instance->parseData();
    }

    public function __destruct()
    {
        print "Done destroying ".$this->num_done." of ".$this->num_total." mortal objects!!".PHP_EOL;
        if ($this->out_mobdb_fp)
            fclose($this->out_mobdb_fp);
        if ($this->out_skill_fp)
            fclose($this->out_skill_fp);
        print "Magick done.".PHP_EOL;
    }
}
