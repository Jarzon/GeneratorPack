<?php declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var callable $_
 * @var callable $e
 * @var string[] $packs
 */
$title = 'Packs';

$this->start('default');
?>
<a class="buttonLink add" href="/admin/generator/createpack">Create new pack</a>

<table class="table">
    <tr>
        <th>Pack</th>
        <th>Entities</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($packs as $pack):
        if($pack['name'] === 'GeneratorPack') continue;
        ?>
        <tr>
            <td><?=$pack['name']?></td>
            <td><?=implode(', ', array_keys($pack['struct']))?></td>
            <td><a href="/admin/generator/modify/<?=$pack['name']?>">Modify</a></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php $this->end() ?>
