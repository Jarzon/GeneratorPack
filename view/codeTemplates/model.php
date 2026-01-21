<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

echo <<<EOT
<?php declare(strict_types=1);

namespace {$file->targetPackNamespace}\Model;

use Jarzon\QueryBuilder\Builder as QB;
use {$file->targetPackNamespace}\Entity\\{$file->entityName};
use \PrimPack\Service\PDO;
use Prim\Model;
use {$file->options['project_name']}\UserPack\Service\User;

class {$file->entityName}Model extends Model
{
    public function __construct(
        PDO \$db,
        array \$options,
        public User \$user
    ) {
        parent::__construct(\$db, \$options);
    }

    public function add{$file->entityName}(array \$data): int
    {
        \$m = new {$file->entityName}();

        \$query = QB::insert(\$m)
            ->columns(\$data)
            ->addColumn(\$m->user_id, \$this->user->id);

        return \$query->exec();
    }

    public function update{$file->entityName}(array \$data, int \${$file->entityNameLC}_id): int
    {
        \$m = new {$file->entityName}();

        \$query = QB::update(\$m)
            ->columns(\$data)
            ->where(\$m->id, '=', \${$file->entityNameLC}_id)
            ->where(\$m->user_id, '=', \$this->user->id);

        return \$query->exec();
    }

    public function delete{$file->entityName}(int \$id): int
    {
        return \$this->update{$file->entityName}(['status' => -1], \$id);
    }

    public function get{$file->entityName}(int \${$file->entityNameLC}_id): object|false
    {
        \$m = new {$file->entityName}();

        \$query = QB::select(\$m)
            ->columns(
EOT;

$select = [];

foreach ($file->data as $row) {
    if(!$row['public']) continue;

    $select[] = "\$m->{$row['name']}";
}

echo implode(', ', $select);

echo <<<EOT
)
            ->where(\$m->id, '=', \${$file->entityNameLC}_id)
            ->where(\$m->user_id, '=', \$this->user->id);

        return \$query->fetch();
    }

    public function getNumberOf{$file->entityName}s(): int
    {
        \$m = new {$file->entityName}();

        \$query = QB::select(\$m)
            ->columns(\$m->id->count()->alias('number'))
            ->where(\$m->user_id, '=', \$this->user->id)
            ->whereRaw(\$m->status, '>=', 0);

        return (int)\$query->fetchColumn();
    }

    public function get{$file->entityName}s(int \$mtart, int \$numberOfElements, string \$orderField, string \$order, array \$columns): array|false
    {
        \$m = new {$file->entityName}();

        \$query = QB::select(\$m)
            ->columns(
EOT;

echo implode(', ', $select);

echo <<<EOT
    )
            ->where(\$m->user_id, '=', \$this->user->id)
            ->limit(\$mtart, \$numberOfElements);

        if(isset(\$columns[\$orderField])) {
            \$query->orderBy(\$columns[\$orderField], \$order);
        } else {
            \$query
                ->orderBy(\$m->status);
        }

        return \$query->fetchAll();
    }
}

EOT;