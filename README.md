# QuickFort Blueprint Parser

Library for parsing QuickFort blueprints.

## Example

### Usage

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

$parser = new Dig();
$parser->setBlueprint($blueprint);
$layers = $parser->getLayers();
```

### Result

```text
[
    ['d'],
    ['d', 'd', 'd'],
    [2 => 'd'],
    [],
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
