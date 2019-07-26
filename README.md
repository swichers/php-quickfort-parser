# QuickFort Blueprint Parser

Library for parsing QuickFort blueprints.

**Example usage:**

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

**Result:**

```
[
    ['d'],
    ['d', 'd', 'd'],
    [2 => 'd'],
    [],
]
```


**Not implemented:**

* Build layer
* Place layer
* Query layer

**Links:**

QuickFort http://www.joelpt.net/quickfort/
