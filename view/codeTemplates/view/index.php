<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

echo <<<EOT
<?php declare(strict_types=1);
/**
 * @var \Prim\View \$this
 * @var callable \$_
 * @var callable \$e
 */

\$title = \$_('{$file->entityNameLC}s');

\$this->start('default');
?>
    <a class="buttonLink add" href="/{$file->entityNameLC}s/create"><?=\$_("add new {$file->entityNameLC}")?></a>

    <?php \$this->insert('table', 'TablePack', ['empty' => "you don't have any {$file->entityNameLC}s", 'type' => '{$file->entityNameLC}s']) ?>

    <?php \$this->insert('sections/pagination', 'BasePack') ?>
<?php \$this->end() ?>
  
EOT;
