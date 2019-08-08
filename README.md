# DP2RA Converter

> Convert Divine Pride item json-fetched info to [rAthena](https://github.com/rathena/rathena)

## Requirements

  * PHP 7.1.3 or newer
  * [Composer](https://getcomposer.org/download/)
  * JSON formatted item info from [Divine-Pride](https://www.divine-pride.net/api/)'s API

## Installation

If you willing to use this as component,

```
composer require cydh/dp2ra
```

or download [latest version](https://github.com/cydh/dp2ra/releases) then install it by using composer

```
composer install
```

## Usage

Then include autoload in PHP file (example: parse_item.php)

```php
require_once './vendor/autoload.php';

// Quick parse item data to output files
Cydh\DP2RA\Items::parse([
    "input" => "path/to/item_db.json",
    "output_itemdb" => "output/path/item_db.txt",
    "output_tradedb" => "output/path/item_trade.txt",
]);

// Quick parse monster data to output files
Cydh\DP2RA\Monsters::parse([
    "input" => "path/to/mob_db.json",
    "output_mobdb" => "output/path/mob_db.txt",
    "output_mobskilldb" => "output/path/mob_skill.txt",
    "output_spawn" => "output/path/spawn.txt",
]);
```

There are manual parsing & file writing by initilizing `Cydh\DP2RA\Items` or `Cydh\DP2RA\Monsters` classes then call its methods.

Some parsers from Divine-Pride data structure (Aegis' enum/value) are written in `public static function` which can be called outside the class.

## Contributing

Make new issue or new pull request. This version is under development

### TODO
  * Items Parser
    * Card compound location
    * Applicable Job
