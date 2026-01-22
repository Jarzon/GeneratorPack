<?php declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \Jarzon\Form $entityForm
 * @var \Jarzon\Form $dataForm
 */

$this->start('default'); ?>
    <div class="box">
        <table>
            <tr id="baseForm">
                <td><?=$dataForm('name')->id('name')->row?></td>
                <td><?=$dataForm('type')->id('type')->row?></td>
                <td><?=$dataForm('min')->id('min')->row?></td>
                <td><?=$dataForm('max')->id('max')->row?></td>
                <td><?=$dataForm('default')->id('default')->row?></td>
                <td><?=$dataForm('public')->id('public')->row?></td>
            </tr>
        </table>

        <?=$entityForm('form')->row?>
            <h3>Entity</h3>
            <div class="listForm"><?=$entityForm('entity_name')->label('Entity name')->row?></div>
            <div class="listForm"><?=$entityForm('crud')->label('Crud')->row?></div>

            <h3>Data</h3>
            <table class="table responsiveTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th>Default</th>
                        <th>Visibility</th>
                        <th>Action</th>
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
                addLine("<?= $line['name']?>", "<?= $line['type']?>","<?= $line['default']?>","<?= $line['public']?>");
                <?php endforeach; ?>
            });
        </script>
    <?php endif; ?>
<?php $this->end() ?>
