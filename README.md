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
