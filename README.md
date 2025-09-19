# QuickFort Blueprint Parser

This is a simple library for parsing basic QuickFort blueprints. Only 'dig' blueprints are implemented at this time.

There are no plans to actively work on this project, but pull requests for new features and layer types will be accepted.

## Installation

The recommended way to install this library is through [Composer](https://getcomposer.org/).

```bash
composer require swichers/php-quickfort-parser
```

## Usage

The following example demonstrates how to parse a 'dig' blueprint.

```php
<?php declare(strict_types=1);

use QuickFort\Parser\Dig;

$blueprint = <<<BLUEPRINT_END
#dig A simple dig blueprint
d,~,~,#
d,d,d,#
~,~,d,#
#,#,#,#
BLUEPRINT_END;

// Create a new Dig parser instance.
$parser = new Dig();

// Set the blueprint text.
$parser->setBlueprint($blueprint);

// Check if the blueprint is a valid 'dig' blueprint.
if (!$parser->checkHeader()) {
    throw new \Exception('Invalid blueprint type.');
}

// Get the parsed layers.
$layers = $parser->getLayers();

// Get the blueprint header information.
$header = $parser->getHeader();
```

### Result

The `$layers` variable will contain a nested array of the parsed blueprint layers:

```text
[
    ['d'],
    ['d', 'd', 'd'],
    [2 => 'd'],
    [],
]
```

The `$header` variable will contain an array of header information:

```text
[
    'command' => 'dig',
    'start' => null,
    'comment' => 'A simple dig blueprint',
]
```

## Not implemented

* Build layer
* Place layer
* Query layer

## Links

* Dwarf Fortress <http://www.bay12games.com/dwarves/>
* QuickFort <http://www.joelpt.net/quickfort/>
* QuickFort (GitHub) <https://github.com/joelpt/quickfort>

[![Build Status](https://travis-ci.com/swichers/php-quickfort-parser.svg?branch=master)](https://travis-ci.com/swichers/php-quickfort-parser)
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/07a22d50e78e4b66b25d0dad19567d81)](https://www.codacy.com/app/swichers/php-quickfort-parser?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=swichers/php-quickfort-parser&amp;utm_campaign=Badge_Grade)
[![Codacy Badge](https://api.codacy.com/project/badge/Coverage/07a22d50e78e4b66b25d0dad19567d81)](https://www.codacy.com/app/swichers/php-quickfort-parser?utm_source=github.com&utm_medium=referral&utm_content=swichers/php-quickfort-parser&utm_campaign=Badge_Coverage)

```text
###*:::=####*:*############################
                           =++##*******++++
                              +++++++++*#++
                                -+++++++*++
                                 -#####**#+
               ......            -##*+*#+#*
           .:=====+--===..       :-#*+*#*#*
            .    :=               .*###*+#+
                 :=                .#######
                 :=                  =#*++*
                 :=  @@@%:           -+####
                 -##@@@@@@:          -++++*
                 -##@####.           =####+
                 -###@@@%-.          =###++
                 -######%@:          =###+*
                 -#####%%@-.         =*##+*
                 -####%%@@@#+.     .=***#++
                 -######@@@@%=.    .***#*++
                 -##=:::-#=---.    +#**++**
               .=#*..   .*#-     -*########
               .+##**.  .*##+..+**#####*+##
#-.:-+###.*################################
###########################################
```
