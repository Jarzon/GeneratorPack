<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

echo <<<EOT
<?php declare(strict_types=1);

use Prim\Container;

use {$file->targetPackNamespace}\Form\\{$file->entityName}Form;
use {$file->targetPackNamespace}\Controller\\{Table, Form, Actions};

return [
    {$file->entityName}Form::class => function(Container \$dic) {
        return [];
    },

    Table::class => function(Container \$dic) {
        \$dic->service('UserPack\User')->verification();

        return [
            \$dic->model('{$file->pack['pack_name']}\\{$file->entityName}Model'),
        ];
    },
    Form::class => function(Container \$dic) {
        \$dic->service('UserPack\User')->verification();

        return [
            \$dic->model('{$file->pack['pack_name']}\\{$file->entityName}Model'),
            \$dic->form('{$file->pack['pack_name']}\\{$file->entityName}Form')
        ];
    },
    Actions::class => function(Container \$dic) {
        \$dic->service('UserPack\User')->verification();

        return [
            \$dic->model('{$file->pack['pack_name']}\\{$file->entityName}Model')
        ];
    },
];

EOT;
