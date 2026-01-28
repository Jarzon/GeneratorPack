<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

echo <<<EOT
<?php declare(strict_types=1);

namespace {$file->targetPackNamespace}\Controller;

use Prim\{View, AbstractController};

use {$file->targetPackNamespace}\Model\\{$file->entityName}Model;

class Actions extends AbstractController
{
    public function __construct(
        View \$view,
        array \$options,
        public {$file->entityName}Model \${$file->entityName}Model
    ) {
        parent::__construct(\$view, \$options);
    }

    public function delete(int \${$file->entityNameLC}Id): void
    {
        \$this->{$file->entityName}Model->delete{$file->entityName}(\${$file->entityNameLC}Id);

        \$this->message('ok', '{$file->entityNameLC} deleted');

        \$this->redirect('/{$file->tableName}/');
    }
}
EOT;
