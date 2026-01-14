<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

echo <<<EOT
<?php declare(strict_types=1);

use Prim\Router;

/** @var Router \$this */

\$this->addGroup('/{$file->entityNameLC}s', function(Router \$r) {
    \$r->both('/[{page:\d+}]', '{$file->pack['pack_name']}\Table', 'index');

    \$r->both('/create', '{$file->pack['pack_name']}\Form', 'add');
    \$r->both('/edit/[{id:\d+}]', '{$file->pack['pack_name']}\Form', 'edit');

    \$r->both('/delete/[{id:\d+}]', '{$file->pack['pack_name']}\Actions', 'delete');
});

EOT;
