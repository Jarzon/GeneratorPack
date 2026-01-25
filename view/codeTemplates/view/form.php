<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 * @var bool $isNew
 */


if($isNew) {
    echo <<<EOT
<?php declare(strict_types=1);
/**
 * @var \Prim\View \$this
 * @var callable \$_
 * @var callable \$e
 * @var {$file->options["project_name"]}\UserPack\Service\User \$user
 * @var {$file->targetPackNamespace}\Entity\\{$file->entityName} \${$file->entityNameLC}
 * @var {$file->targetPackNamespace}\Form\\{$file->entityName}Form \$form
 * @var bool \$new
 */

\$this->start('default'); ?>
    <div class="box">
        <form action="/{$file->entityNameLC}s/<?=(\$new)? 'create': "edit/\${$file->entityNameLC}->id"?>" method="POST">
            
EOT;
}

foreach ($file->data as $row) {
    if($row['public'] === 'private') continue;

    echo "<div class=\"listForm\"><?=\$form('{$row['name']}')->label(\$_('{$row['name']}'))->row?></div>
            ";
}

if($isNew) {
    echo <<<EOT

            <?=\$form('submit')->value(\$_('save {$file->entityNameLC}'))->row?>
        </form>
    </div>
<?php \$this->end() ?>

EOT;
}