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
    \$r->both('/[{page:\d+}]', '{$file->packName}\Table', 'index');

    \$r->both('/create', '{$file->packName}\Form', 'add');
    \$r->both('/edit/[{id:\d+}]', '{$file->packName}\Form', 'edit');

    \$r->both('/delete/[{id:\d+}]', '{$file->packName}\Actions', 'delete');
});

EOT;
