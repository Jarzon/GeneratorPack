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

namespace {$file->targetPackNamespace}\Entity;

use Jarzon\QueryBuilder\Entity\EntityBase;

class {$file->entityName} extends EntityBase
{

EOT;
}
foreach($file->data as $row):
    if(!$isNew && $row['status'] !== '1' && $row['status'] !== '2') continue;
    $type = $file->getColumnType($row['type']);
    ?>
    public <?=$type ?> $<?=$row['name'] ?>;
<?php endforeach;

if($isNew): ?>
}
<?php endif; ?>
