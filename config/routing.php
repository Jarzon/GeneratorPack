<?php declare(strict_types=1);

/** @var $this Router */

use Prim\Router;

$this->addGroup('/admin/generator', function(Router $r) {
    $r->both('/', 'GeneratorPack\Generator', 'index');
    $r->both('/createpack', 'GeneratorPack\Generator', 'createpack');
    $r->both('/modify/{packName}[/{entityName}]', 'GeneratorPack\Generator', 'modify');
});
