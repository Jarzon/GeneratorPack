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

\$this->addGroup('/{$file->tableName}', function(Router \$r) {
    \$r->both('/{page:\d*}', '{$file->packName}\Table', 'index');
    \$r->both('/showDeleted/[{page:\d+}]', '{$file->packName}\Table', 'showDeleted');
    \$r->both('/search/{page:\d*}', '{$file->packName}\Table', 'searchResults');
    \$r->both('/showDeleted/search/{page:\d*}', '{$file->packName}\Table', 'searchDeletedResults');

    \$r->both('/create', '{$file->packName}\Form', 'add');
    \$r->both('/edit/[{id:\d+}]', '{$file->packName}\Form', 'edit');

    \$r->both('/delete/[{id:\d+}]', '{$file->packName}\Actions', 'delete');
});

EOT;
