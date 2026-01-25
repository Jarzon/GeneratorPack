<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

$entityFileTable = "{$file->entityName}Table";

$select = [];

foreach ($file->data as $row) {
    if(!$row['public']) continue;

    $select[] = "\$m->{$row['name']}";
}

$selectColumns = implode(', ', $select);

echo <<<EOT
<?php declare(strict_types=1);

namespace {$file->targetPackNamespace}\Model;

use Jarzon\QueryBuilder\Builder as QB;
use {$file->targetPackNamespace}\Entity\\{$file->getEntityTableName()};
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

    /** @param array<mixed> \$data */
    public function add{$file->entityName}(array \$data): int
    {
        \$m = new {$file->getEntityTableName()}();

        \$query = QB::insert(\$m)
            ->columns(\$data)
            ->addColumn(\$m->user_id, \$this->user->id);

        return \$query->exec();
    }

    /** @param array<mixed> \$data */
    public function update{$file->entityName}(array \$data, int \${$file->entityNameLC}_id): int
    {
        \$m = new {$file->getEntityTableName()}();

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
        \$m = new {$file->getEntityTableName()}();

        \$query = QB::select(\$m)
            ->columns($selectColumns)
            ->where(\$m->id, '=', \${$file->entityNameLC}_id)
            ->where(\$m->user_id, '=', \$this->user->id);

        return \$query->fetch();
    }

    public function getNumberOf{$file->entityName}s(): int
    {
        \$m = new {$file->getEntityTableName()}();

        \$query = QB::select(\$m)
            ->columns(\$m->id->count()->alias('number'))
            ->where(\$m->user_id, '=', \$this->user->id)
            ->whereRaw(\$m->status, '>=', 0);

        return (int)\$query->fetchColumn();
    }

    /**
     * @param array<mixed> \$columns
     * @return array<mixed>|false
     */
    public function get{$file->entityName}s(int \$mtart, int \$numberOfElements, string \$orderField, string \$order, array \$columns): array|false
    {
        \$m = new {$file->getEntityTableName()}();

        \$query = QB::select(\$m)
            ->columns($selectColumns)
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