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
    <a class="buttonLink add" href="/admin/generator/create">Create new pack</a>

<table class="table">
    <tr>
        <th>Pack</th>
    </tr>
    <?php foreach ($packs as $pack):
        if($pack === 'GeneratorPack') continue;
        ?>
        <tr>
            <td><?=$pack?></td>
        </tr>
    <?php endforeach; ?>
</table>

<?php $this->end() ?>
