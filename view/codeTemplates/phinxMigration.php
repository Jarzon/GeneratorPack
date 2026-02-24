<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 * @var bool $isNew
 * @var array $migrationName
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

$className = ucfirst($file->tableName). implode('', array_map(fn($s) => ucfirst($s), $migrationName));

echo <<<EOT
<?php declare(strict_types=1);
use Phinx\Migration\AbstractMigration;

class {$className} extends AbstractMigration
{
    public function change(): void
    {
        \$table = \$this->table('{$file->tableName}');
        \$table

EOT;

// TODO: more precise type based on limit(eg. biginteger)
$previousColumnName = '';
foreach ($file->data as $row) {
    if($row['name'] === 'id') {
        if($row['status'] === '0' || $row['status'] === '2') $previousColumnName = $row['name'];
        continue;
    }

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

        if($row['type'] === 'currency' && !empty($row['min']) && !empty($row['max'])) {
            $integerNumber = (int)$row['min'];
            $decimalNumber = (int)$row['max'];
            $scale = $integerNumber + $decimalNumber;
            echo "'scale' => $scale, 'precision' => $decimalNumber, ";
        }
        else if(!empty($row['max'])) {
            echo "'limit' => {$row['max']}, ";
        }
        if($row['type'] === 'text') {
            if($row['default'] === 'null') {
                echo "'null' => true, ";
            } else {
                echo "'null' => false, ";
            }
        }
        if(isset($row['default']) && $row['default'] !== '' && $row['default'] !== 'null') {
            echo "'default' => '{$row['default']}', ";
        }

        if(!$isNew && $previousColumnName !== '') {
            echo "'after' => '{$previousColumnName}', ";
        }

        echo "]";
    }

    if($row['status'] !== '0') echo ")\n";
    if($row['status'] === '0' || $row['status'] === '2') $previousColumnName = $row['name'];
} ?>
            -><?=$isNew? 'create' : 'update' ?>();
    }
}
