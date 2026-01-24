<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
  * @var bool $isNew
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

$className = $file->entityName. ($isNew? 'Init' : 'Update');

echo <<<EOT
<?php declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

class {$className} extends AbstractMigration
{
    public function change(): void
    {
        \$table = \$this->table('{$file->entityNameLC}');
        \$table

EOT;

// TODO: more precise type based on limit(eg. biginteger)
foreach ($file->data as $row) {
    if($row['name'] === 'id') continue;

    $isFullLine = false;

    // new line
    if($row['status'] === '1') {
        echo "            ->addColumn('{$row['name']}', '{$columnsType[$row['type']]}'";
        $isFullLine = true;
    }
    // updated line
    else if($row['status'] === '2') {
        echo "            ->changeColumn('{$row['name']}', '{$columnsType[$row['type']]}'";
        $isFullLine = true;
    }
    // deleted line
    else if($row['status'] === '-1') {
        echo "            ->removeColumn('{$row['name']}'";
    }

    if(
        $isFullLine
        && (
            !empty($row['max'])
            || $row['type'] === 'text'
            || (isset($row['default']) && $row['default'] !== '')
        )
    ) {
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

    if($row['status'] !== '0') echo ")\n";

} ?>
            -><?=$isNew? 'create' : 'save' ?>();
    }
}
