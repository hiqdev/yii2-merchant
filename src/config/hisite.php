<?php

return [
    'components' => [
        'themeManager' => [
            'class' => \hiqdev\thememanager\ThemeManager::class,
            'pathMap' => [
                '@hiqdev/yii2/merchant/views' => '$themedViewPaths',
            ],
        ],
    ],
];
