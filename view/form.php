<?php declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \Jarzon\Form $entityForm
 * @var \Jarzon\Form $dataForm
 * @var array $entities
 * @var string|null $entityName
 * @var string|null $packName
 * @var array $lines
 * @var array $newCode
 */

$this->start('default'); ?>
    <?php foreach($newCode as $i => $code):
        if(trim($code) === '') continue;
    ?>
        <div>
            <h3><?=$i ?></h3>
            <pre><?=htmlentities($code) ?></pre>
        </div>
    <?php endforeach; ?>
    <div class="box">
        <table>
            <tr id="baseForm">
                <td><?=$dataForm('name')->class('name')->row?></td>
                <td><?=$dataForm('type')->class('type')->row?></td>
                <td><?=$dataForm('min')->class('min')->row?></td>
                <td><?=$dataForm('max')->class('max')->row?></td>
                <td><?=$dataForm('default')->class('default')->row?></td>
                <td><?=$dataForm('public')->class('public')->row?></td>
                <td><?=$dataForm('status')->class('status')->row?></td>
            </tr>
        </table>

        <?php if(!empty($entities)): ?>
            <table class="table responsiveTable">
                <tr>
                    <th>Entity</th>
                    <th>Actions</th>
                </tr>
                <?php foreach($entities as $entity): ?>
                    <tr>
                        <td><?=$entity ?></td>
                        <td><a href="/admin/generator/modify/<?=$packName ?>/<?=$entity ?>">Modify</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <?=$entityForm('form')->row?>
            <?php if($entityName === null): ?>
                <h3>New entity</h3>
                <div class="listForm"><?=$entityForm('entity_name')->label('Entity name')->row?></div>
                <div class="listForm"><?=$entityForm('crud')->label('Crud')->row?></div>
                <div class="listForm"><?=$entityForm('disableCodeGeneration')->label('Disable code generation')->row?></div>
            <?php endif; ?>

            <h3><?=$entityName ?></h3>
            <table class="table responsiveTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th>Default</th>
                        <th>Visibility</th>
                        <th colspan="2">Action</th>
                    </tr>
                </thead>
                <tbody id="lines"></tbody>
            </table>

            <div class="buttonLink" id="add">Add line</div>

            <hr class="separator">

        <?=$entityForm('save')->value('Save')->row?>

        <?=$entityForm('/form')->row?>
    </div>
<?php $this->end() ?>

<?php $this->start('css') ?>
<style>
    .actions div {
        display: inline-block;
    }

    input[type="text"], input[type="number"] {
        width: 100%;
    }
</style>
<?php $this->end() ?>

<?php $this->start('js') ?>
    <script src="<?=$this->fileCache("/js/generator.js") ?>" defer></script>
    <?php if(isset($lines)): ?>
        <script>
            window.addEventListener('load', function () {
                <?php foreach($lines as $line): ?>
                addLine("<?= $line['name']?>", "<?= $line['type']?>", "<?= $line['min']?>", "<?= $line['max']?>", "<?= $line['default']?>" ,"<?= $line['public']?>");
                <?php endforeach; ?>
            });
        </script>
    <?php endif; ?>
<?php $this->end(); ?>

<?php $this->insert('include', 'GeneratorPack'); ?>
