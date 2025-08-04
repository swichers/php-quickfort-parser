<?php declare(strict_types=1);

namespace QuickFort\tests\Unit\DataProviders;

class BlueprintsToValues
{
    public static function simpleLines(): array
    {
        return [
            [
                [
                    '#dig',
                    'd,d,d,#',
                    'd,~,i,#',
                    'j,`,d,#',
                    '#,#,#,#',
                ],
                [
                    [
                        ['d', 'd', 'd'],
                        [0 => 'd', 2 => 'i'],
                        [0 => 'j', 2 => 'd'],
                        [],
                    ],
                ]
            ],
            [
                [
                    '#dig',
                    'd,d,d,#',
                    '~,~,~,#',
                    'd,d,d,d,#',
                    '#,#,#,#',
                ],
                [
                    [
                        ['d', 'd', 'd'],
                        [],
                        ['d', 'd', 'd', 'd'],
                        [],
                    ],
                ]
            ],
        ];
    }

    public static function layeredLines(): array
    {
        return [
            [
                [
                    '# dig',
                    '`,`,`,#',
                    'j,`,j,#',
                    '`,`,`,#',
                    '#>,#,#,#',
                    'u,d,d,#',
                    'd,d,d,#',
                    'd,d,d,#',
                    '#<,#,#,#',
                    'j,d,j,#',
                    'j,d,j,#',
                    'd,j,d,#',
                    '#,#,#,#',
                ],
                [
                    [
                        [],
                        [0 => 'j', 2 => 'j'],
                        [],
                    ],
                    [
                        [],
                        ['j', 'd', 'j'],
                        ['j', 'd', 'j'],
                        ['d', 'j', 'd'],
                        [],
                    ],
                    [
                        [],
                        ['u', 'd', 'd'],
                        ['d', 'd', 'd'],
                        ['d', 'd', 'd'],
                    ],
                ],
            ],
            [
                [
                    '#dig Stairs leading down to a small room below',
                    'j,`,`,#',
                    '`,`,`,#',
                    '`,`,`,#',
                    '#>,#,#,#',
                    'u,d,d,#',
                    'd,d,d,#',
                    'd,d,d,#',
                    '#,#,#,#',
                ],
                [
                    [
                        ['j'],
                        [],
                        [],
                    ],
                    [
                        [],
                        ['u', 'd', 'd'],
                        ['d', 'd', 'd'],
                        ['d', 'd', 'd'],
                        [],
                    ],
                ]
            ],
            [
                [
                    '#dig Stairs leading down to a small room below',
                    'j,`,`,#',
                    '`,`,`,#',
                    '`,`,`,#',
                    '#<,#,#,#',
                    'd,j,i,#',
                    'd,d,d,#',
                    'd,d,d,#',
                    '#,#,#,#',
                    '#<,#,#,#',
                    'u,u,u,#',
                    'd,d,d,#',
                    'd,d,d,#',
                    '#,#,#,#',
                ],
                [
                    [
                        [],
                        ['d', 'j', 'i'],
                        ['d', 'd', 'd'],
                        ['d', 'd', 'd'],
                        [],
                    ],
                    [
                        [],
                        ['u', 'u', 'u'],
                        ['d', 'd', 'd'],
                        ['d', 'd', 'd'],
                        [],
                    ],
                    [
                        ['j'],
                        [],
                        [],
                    ],
                ]
            ],
        ];
    }

    public static function commandExpansion(): array
    {
        return [
            [
                [
                    '#dig',
                    'd(3x3),#',
                    '~,~,~,#',
                    '`,`,`,#',
                    '#,#,#,#',
                ],
                [
                    [
                        ['d', 'd', 'd'],
                        ['d', 'd', 'd'],
                        ['d', 'd', 'd'],
                        [],
                    ],
                ]
            ]
        ];
    }
}
