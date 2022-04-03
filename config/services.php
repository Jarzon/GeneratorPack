<?php declare(strict_types=1);

use GeneratorPack\Controller\Generator;
use GeneratorPack\Service\File;
use Prim\Container;

return [
    Generator::class => function(Container $dic) {
        $user = $dic->service('UserPack\User');
        $user->verification();
        if(!$user->isAdmin()) {
            header('location: /dashboard');
        };

        return [
            $dic->form('GeneratorPack\PackForm'),
            $dic->form('GeneratorPack\DataForm'),
            $dic->service('GeneratorPack\File'),
        ];
    },
    File::class => function(Container $dic) {
        return [
            $dic->options
        ];
    }
];
