<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

// TODO: based $this->data generate the columns
echo <<<EOT
<?php declare(strict_types=1);
/**
 * @var \Prim\View \$this
 * @var callable \$_
 * @var callable \$e
 * @var {$file->options["project_name"]}\BasePack\Service\ActionsMenu \$_actionMenu
 * @var {$file->options["project_name"]}\UserPack\Service\User \$user
 * @var {$file->targetPackNamespace}\Entity\\{$file->entityName} \${$file->entityNameLC}
 * @var {$file->targetPackNamespace}\Form\\{$file->entityName}Form \$form
 */

    if(!\$new && \${$file->entityNameLC}->status === 0) {
        \$_actionMenu
            ->addSubAction(\$_('delete the %s', 'the {$file->entityNameLC}'), 'delete', "/{$file->entityNameLC}s/delete/\${$file->entityNameLC}->id")
            ->confirmation(\$_('are you sure you want to delete %s?', 'this {$file->entityNameLC}'));
    }

\$this->start('default'); ?>
    <div class="box">
        <form action="/{$file->entityNameLC}s/<?=(\$new)? 'create': "edit/\${$file->entityNameLC}->id"?>" method="POST">
            
EOT;

foreach ($file->data as $row) {
    if($row['public'] === 'private') continue;

    echo "<div class=\"listForm\"><?=\$form('{$row['name']}')->label(\$_('{$row['name']}'))->row?></div>
            ";
}

echo <<<EOT

            <?=\$form('submit')->value(\$_('save {$file->entityNameLC}'))->row?>
            <a class="cancel_button" href="<?=cancel('/{$file->entityNameLC}s/')?>"><?=\$_("cancel")?></a>
        </form>
    </div>
<?php \$this->end() ?>

<?php \$this->start('js') ?>
    <script>
        window.addEventListener('load', function (e) {
            let dates = new DatePicker('input[type="date"]', '<?=\$getLanguage()?>');
        });
    </script>
<?php \$this->end() ?>

EOT;