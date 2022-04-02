<?php declare(strict_types=1);

use GeneratorPack\Controller\Generator;
use GeneratorPack\Service\File;
use Prim\Container;

return [
    Generator::class => function(Container $dic) {
        $user = $dic->get('userService');
        $user->verification();
        if(!$user->isAdmin()) {
            header('location: /dashboard');
        };

        return [
            $dic->form('GeneratorPack\PackForm'),
            $dic->form('GeneratorPack\DataForm'),
            $dic->get('fileService'),
        ];
    },
    File::class => function(Container $dic) {
        return [
            $dic->options
        ];
    }
];
