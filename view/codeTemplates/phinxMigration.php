<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

$columnsType = [
    'text' => 'string',
    'textarea' => 'text',
    'number' => 'integer',
    'float' => 'float',
    'range' => 'number',
    'currency' => 'decimal',
    'date' => 'date',
    'datetime' => 'datetime',
    'time' => 'time',
    'email' => 'string',
    'url' => 'string',
    'file' => 'string',
    'tel' => 'string',
    'hidden' => 'string',
    'color' => 'string',
    'password' => 'string',
];

echo <<<EOT
<?php declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

class {$file->entityName}Init extends AbstractMigration
{
    public function change()
    {
        \$table = \$this->table('{$file->entityNameLC}');
        \$table

EOT;

// TODO: more precise type based on limit(eg. biginteger)
foreach ($file->data as $row) {
    if($row['name'] === 'id') continue;

    echo "            ->addColumn('{$row['name']}', '{$columnsType[$row['type']]}'";
    if(!empty($row['max']) || $row['type'] === 'text' || (isset($row['default']) && $row['default'] !== '')) {
        echo ', [';

        if(!empty($row['max'])) {
            echo "'limit' => {$row['max']}, ";
        }
        if($row['type'] === 'text') {
            echo "'null' => false, ";
        }
        if(isset($row['default']) && $row['default'] !== '') {
            echo "'default' => '{$row['default']}'";
        }

        echo "]";
    }

    echo ")\n";

} ?>
            ->create();
    }
}
