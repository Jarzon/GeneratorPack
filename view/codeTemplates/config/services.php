<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

echo <<<EOT
<?php declare(strict_types=1);

use Prim\Container;

use {$file->targetPackNamespace}\Controller\\{Table, Form, Actions};

return [
    Table::class => function(Container \$dic) {
        \$user = \$dic->service('UserPack\User');
        \$user->verification();

        return [
            \$dic->model('{$file->packName}\\{$file->entityName}Model'),
            \$user,
        ];
    },
    Form::class => function(Container \$dic) {
        \$dic->service('UserPack\User')->verification();

        return [
            \$dic->model('{$file->packName}\\{$file->entityName}Model'),
            \$dic->form('{$file->packName}\\{$file->entityName}Form')
        ];
    },
    Actions::class => function(Container \$dic) {
        \$dic->service('UserPack\User')->verification();

        return [
            \$dic->model('{$file->packName}\\{$file->entityName}Model')
        ];
    },
];

EOT;
