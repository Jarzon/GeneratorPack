<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

echo <<<EOT
<?php declare(strict_types=1);

namespace {$file->targetPackNamespace}\Entity;

use Jarzon\QueryBuilder\Entity\EntityBase;
use Jarzon\QueryBuilder\Columns\{Numeric, Text, Date};

class {$file->entityName} extends EntityBase
{

EOT;
foreach($file->data as $row):
    $type = $file->getColumnType($row['type']);
    ?>
    public <?=$type ?> $<?=$row['name'] ?>;
<?php endforeach; ?>
}
