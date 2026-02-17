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
use {$file->options['project_name']}\UserPack\Service\User;

use {$file->options['project_name']}\TablePack\Service\Table as TableService;
use {$file->targetPackNamespace}\Entity\\{$file->entityName}Table;
use {$file->targetPackNamespace}\Model\\{$file->entityName}Model;

class Table extends AbstractController
{
    public function __construct(
        View \$view,
        array \$options,
        public {$file->entityName}Model \${$file->entityName}Model,
        public User \$user,
    ) {
        parent::__construct(\$view, \$options);
    }
    
    public function showDeleted(int \$page = 1): void
    {
        \$this->index(\$page, true);
    }

    public function searchDeletedResults(int \$page = 1): void
    {
        if(isset(\$_POST['search']) && \$_POST['search'] !== '') {
            \$_SESSION['lastSearch'] = trim(str_replace('/', '-', \$_POST['search']));
        }

        \$this->index(\$page, true, \$_SESSION['lastSearch'] ?? '');
    }

    public function searchResults(int \$page = 1): void
    {
        if(isset(\$_POST['search']) && \$_POST['search'] !== '') {
            \$_SESSION['lastSearch'] = trim(str_replace('/', '-', \$_POST['search']));
        }

        \$this->index(\$page, false, \$_SESSION['lastSearch'] ?? '');
    }

    public function index(int \$page = 1, bool \$showDeleted = false, string \$search = ''): void
    {
        \$paginator = new Pagination(\$page, \$this->{$file->entityName}Model->getNumberOf{$file->entityName}s(), 13, 3);

        \$t = new {$file->entityName}Table();

        \$table = new TableService('table');

        \$table->setTableClass('table', 'responsiveTable')

EOT;

foreach ($file->data as $row) {
    if($row['public'] === 'private') continue;

    echo "            ->th('{$row['name']}')->order(\$t->{$row['name']})";
    if($row['type'] === 'string' || $row['type'] === 'text') echo "->escape()";
    echo "\n";
}

echo <<<EOT
            ->th('actions');
            
            if(\$showDeleted) {
                \$table->addAction('restore', '/{$file->tableName}/restore/');
            } else {
                \$table->addAction('modify', '/{$file->tableName}/edit/');
            }

        \${$file->entityNameLC}s = \$this->{$file->entityName}Model->get{$file->entityName}s(\$paginator->getFirstPageElement(), \$paginator->getElementsPerPages(), \$table->getOrderColumn(), \$table->getOrder(), \$table->getOrderColumns(), \$showDeleted, \$search);

        \$table->rows(\${$file->entityNameLC}s);

        \$this->render('index', '{$file->entityName}Pack', [
            'paginator' => \$paginator,
            'table' => \$table,
            'showDeleted' => \$showDeleted,
            'searchTerm' => \$search,
        ]);
    }
}

EOT;