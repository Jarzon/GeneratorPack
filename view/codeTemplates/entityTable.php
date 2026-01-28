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

class {$file->entityName}Table extends EntityBase
{

EOT;
foreach($file->data as $row):
    if($row['status'] === '-1') continue;
    $type = $file->getTableColumnType($row['type'], true);
    ?>
    public <?=$type ?> $<?=$row['name'] ?>;
<?php endforeach; ?>

    public function __construct(string $alias = '')
    {
        parent::__construct($alias, <?=$file->entityName ?>::class);

        $this->table('<?=$file->tableName ?>');

<?php foreach($file->data as $row):
    if($row['status'] === '-1') continue;
    $type = $file->getTableColumnType($row['type']);
    ?>
        $this-><?=$row['name'] ?> = $this-><?=$type ?>('<?=$row['name'] ?>');
<?php endforeach; ?>
    }
}
