<?php declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \Jarzon\Form $packForm
 */

$this->start('default'); ?>
    <div class="box">

        <?=$packForm('form')->row?>
                <h3>Pack</h3>
                <div class="listForm"><?=$packForm('pack_name')->label('Pack name')->row?></div>

            <?=$packForm('save')->value('Save')->row?>

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

<?php $this->end() ?>
