<?php declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \Jarzon\Form $packForm
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

        <?=$packForm('form')->row?>
            <h3>Pack</h3>
            <div class="listForm"><?=$packForm('pack_name')->label('Pack name')->row?></div>
            <div class="listForm"><?=$packForm('entity_name')->label('Entity name')->row?></div>
            <div class="listForm"><?=$packForm('crud')->label('Crud')->row?></div>

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

            <?=$packForm('submit')->value('Save')->row?>

        <?=$packForm('/form')->row?>
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
    <script src="/js/generator.js" defer></script>
<?php $this->end() ?>
