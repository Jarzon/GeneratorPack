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
use PaginationPack\Service\Pagination;

use {$file->options['project_name']}\TablePack\Service\Table as TableService;
use {$file->targetPackNamespace}\Entity\\{$file->entityName};
use {$file->targetPackNamespace}\Model\\{$file->entityName}Model;

class Table extends AbstractController
{
    public function __construct(
        View \$view,
        array \$options,
        public {$file->entityName}Model \${$file->entityName}Model
    ) {
        parent::__construct(\$view, \$options);
    }

    public function index(int \$page = 1): void
    {
        \$paginator = new Pagination(\$page, \$this->{$file->entityName}Model->getNumberOf{$file->entityName}s(), 13, 3);

        \$t = new {$file->entityName}();

        \$table = new TableService('table');

        \$table->setTableClass('table', 'responsiveTable')

EOT;

foreach ($file->data as $row) {
    if($row['public'] === 'private') continue;

    echo "            ->th('{$row['name']}')->order(\$t->{$row['name']})->escape()\n";
}

echo <<<EOT
            ->th('actions')->colspan(1)
            ->addAction('modify', '/{$file->tableName}/edit/');

        \${$file->entityNameLC}s = \$this->{$file->entityName}Model->get{$file->entityName}s(\$paginator->getFirstPageElement(), \$paginator->getElementsPerPages(), \$table->getOrderColumn(), \$table->getOrder(), \$table->getOrderColumns());

        \$table->rows(\${$file->entityNameLC}s);

        \$this->render('index', '{$file->entityName}Pack', [
            'paginator' => \$paginator,
            'table' => \$table
        ]);
    }
}

EOT;