# DP2RA Converter

> Convert Divine Pride item json-fetched info to [rAthena](https://github.com/rathena/rathena)

## Requirements

  * PHP 7.1.3 or newer
  * [Composer](https://getcomposer.org/download/)
  * JSON formatted item info from [Divine-Pride](https://www.divine-pride.net/api)'s API

## Installation

Just in your woring directory

```
composer require cydh/dp2ra
```

Then include autoload in PHP file (example: parse_item.php)

```php
require_once './vendor/autoload.php';

Cydh\DP2RA\Items::parse([
    "input" => "path/to/item_db.json",
    "output_itemdb" => "output/path/item_db.txt",
    "output_tradedb" => "output/path/item_trade.txt",
]);
```

## Contributing

Make new issue or new pull request. This version is under development

### TODO
  * Items Parser
    * Card compound location
    * Applicable Job
  * Monster Parser
