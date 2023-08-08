<?php declare(strict_types=1);

namespace GeneratorPack\Service;

class File
{
    public array $options;
    public array $pack;
    public array $data;

    public string $packDir;
    public string $entityName;
    public string $entityNameLC;
    public string $targetPackNamespace;

    public function __construct(array $options = [])
    {
        $this->options = $options += [
            'project_name' => ''
        ];
    }

    public function setPack(array $packValues): void
    {
        if(!str_contains($packValues['pack_name'], 'Pack')) {
            $packValues['pack_name'] = $packValues['pack_name'] . 'Pack';
        }

        $packValues['pack_name'] = ucfirst($packValues['pack_name']);

        if(!str_contains($packValues['pack_name'], 'Pack')) {
            $packValues['entity_name'] = str_replace('Pack', '', $packValues['entity_name']);
        }

        $this->entityName = ucfirst($packValues['entity_name']);
        $this->entityNameLC = lcfirst($packValues['entity_name']);

        $this->targetPackNamespace = "{$this->options['project_name']}\\{$packValues['pack_name']}";
        $this->pack = $packValues;
        $this->packDir = $this->options['root'] . 'src/' . $packValues['pack_name'];
    }

    public function setData(array $dataValues): void
    {
        $this->data = $dataValues;
    }

    public function createPack(): void
    {
        $this->createDir($this->packDir);

        $this->generateTableEntity();
        $this->generateEntity();
        $this->generatePhinx();
        $this->generateForm();
        $this->generateModel();

        if($this->pack['crud'] === true) {
            $this->generateRouting();
            $this->generateServices();
            $this->generateControllers();
            $this->generateViews();
        }
    }

    private function getTableColumnType(string $type, bool $isType = false): string
    {
        if (in_array($type, ['string', 'text', 'textarea', 'email', 'url', 'file', 'tel', 'hidden', 'color', 'password'])) {
            $type = 'text';
        } else if (in_array($type, ['number', 'float', 'range', 'currency'])) {
            $type = $isType? 'Numeric' : 'number';
        } else if (in_array($type, ['datetime', 'date', 'time'])) {
            $type = 'date';
        }

        if($isType) {
            $type = ucfirst($type);
        }

        return $type;
    }

    private function getColumnType(string $type): string
    {
        if (in_array($type, ['string', 'text', 'textarea', 'email', 'url', 'file', 'tel', 'hidden', 'color', 'password', 'datetime', 'date', 'time', 'currency'])) {
            $type = 'string';
        }
        else if (in_array($type, ['number', 'range'])) {
            $type = 'int';
        }
        else if (in_array($type, ['float'])) {
            $type = 'float';
        }

        return $type;
    }

    public function generateTableEntity(): void
    {
        $entityDir = $this->packDir . '/Entity';

        $this->createDir($entityDir);

        $file = <<<EOT
<?php declare(strict_types=1);

namespace {$this->targetPackNamespace}\Entity;

use Jarzon\QueryBuilder\Entity\EntityBase;
use Jarzon\QueryBuilder\Columns\{Numeric, Text, Date};

class {$this->entityName}Table extends EntityBase
{

EOT;
        // Proprieties
        foreach ($this->data as $row) {
            $type = $this->getTableColumnType($row['type'], true);
            $file .= "    public {$type} \${$row['name']};\n";
        }

        $file .= "
    public function __construct(string \$alias = '')
    {
        parent::__construct(\$alias);

        \$this->table('{$this->entityNameLC}');

";

        foreach ($this->data as $row) {
            $type = $this->getTableColumnType($row['type']);

            $file .= "        \$this->{$row['name']} = \$this->{$type}('{$row['name']}');\n";
        }

        $file .= '    }
}
';
        $this->createFile("$entityDir/{$this->entityName}Table.php", $file);
    }

    public function generateEntity(): void
    {
        $entityDir = $this->packDir . '/Entity';

        $this->createDir($entityDir);

        $file = <<<EOT
<?php declare(strict_types=1);

namespace {$this->targetPackNamespace}\Entity;

use Jarzon\QueryBuilder\Entity\EntityBase;
use Jarzon\QueryBuilder\Columns\{Numeric, Text, Date};

class {$this->entityName} extends EntityBase
{

EOT;
        // Proprieties
        foreach ($this->data as $row) {
            $type = $this->getColumnType($row['type']);
            $file .= "    public {$type} \${$row['name']};\n";
        }

        $file .= '
}
';
        $this->createFile("$entityDir/{$this->entityName}.php", $file);
    }

    public function generatePhinx(): void
    {
        $phinxDir = $this->packDir . '/phinx';
        $this->createDir($phinxDir);

        $phinxDir = $phinxDir . '/migrations/';
        $this->createDir($phinxDir);

        $file = <<<EOT
<?php
use Phinx\Migration\AbstractMigration;

class {$this->entityName}Init extends AbstractMigration
{
    public function change()
    {
        \$table = \$this->table('{$this->entityNameLC}');
        \$table

EOT;
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

        // TODO: more precise type based on limit(eg. biginteger)
        foreach ($this->data as $row) {
            if($row['name'] === 'id') continue;

            $file .= "            ->addColumn('{$row['name']}', '{$columnsType[$row['type']]}'";
            if(!empty($row['max']) || $row['type'] === 'text' || (isset($row['default']) && $row['default'] !== '')) {
                $file .= ', [';

                if(!empty($row['max'])) {
                    $file .= "'limit' => {$row['max']}, ";
                }
                if($row['type'] === 'text') {
                    $file .= "'null' => false, ";
                }
                if(isset($row['default']) && $row['default'] !== '') {
                    $file .= "'default' => '{$row['default']}'";
                }

                $file .= "]";
            }

            $file .= ")\n";

        }

        $file .= '            ->create();
        }
}
';

        $this->createFile($phinxDir . date('YmdHis') . "_{$this->entityNameLC}_init.php", $file);
    }

    public function generateForm(): void
    {
        $formDir = $this->packDir . '/Form';

        $file = <<<EOT
<?php declare(strict_types=1);

namespace {$this->targetPackNamespace}\Form;

use Jarzon\FormAbstract;

class {$this->entityName}Form extends FormAbstract
{

    public function __construct()
    {
        parent::__construct();

        \$this->build();
    }
    
    public function build(): void
    {
        \$this->form

EOT;

        foreach ($this->data as $row) {
            if($row['public'] === 'private') continue;

            $file .= $this->generateFormLine($row);
        }

        $file .= '        ->submit();
    }
     
    public function buildAdmin(): void
    {
        $this->form
        ';

        foreach ($this->data as $row) {
            if($row['public'] === 'public') continue;

            $file .= $this->generateFormLine($row);
        }
        $file .= '        ;
     }
}
';
        $this->createDir($formDir);
        $this->createFile("$formDir/{$this->entityName}Form.php", $file);
    }

    protected function generateFormLine(array $row): string
    {
        $type = $row['type'];

        if($type === 'datetime') {
            $type = 'text';
        }

        $file = "            ->{$type}('{$row['name']}')\n";

        if(!empty($row['max']) || $row['type'] === 'string'  || $row['type'] === 'text' || isset($row['default'])) {
            if(!empty($row['min'])) {
                $file .= "            ->min({$row['min']})\n";
            }
            if(!empty($row['max'])) {
                $file .= "            ->max({$row['max']})\n";
            }
        }

        return $file;
    }

    public function generateRouting(): void
    {
        $configDir = $this->packDir . '/config';
        $this->createDir($configDir);

        $routingFile = "$configDir/routing.php";

        if(file_exists($routingFile)) {
            $routingFile = "$configDir/routing_$this->entityNameLC.php";
        }

        $this->createFile("$routingFile", <<<EOT
<?php declare(strict_types=1);

/** @var Router \$this */

use Prim\Router;

\$this->addGroup('/{$this->entityNameLC}s', function(Router \$r) {
    \$r->both('/[{page:\d+}]', '{$this->pack['pack_name']}\Table', 'index');

    \$r->both('/create', '{$this->pack['pack_name']}\Form', 'add');
    \$r->both('/edit/[{id:\d+}]', '{$this->pack['pack_name']}\Form', 'edit');

    \$r->both('/delete/[{id:\d+}]', '{$this->pack['pack_name']}\Actions', 'delete');
});

EOT);
    }

    public function generateServices(): void
    {
        $servicesFile = "$this->packDir/config/services.php";

        if(file_exists($servicesFile)) {
            $servicesFile = "$this->packDir/config/services_$this->entityNameLC.php";
        }

        $this->createFile($servicesFile, <<<EOT
<?php declare(strict_types=1);

use Prim\Container;

use {$this->targetPackNamespace}\Form\\{$this->entityName}Form;
use {$this->targetPackNamespace}\Controller\\{Table, Form, Actions};

return [
    {$this->entityName}Form::class => function(Container \$dic) {
        return [];
    },

    Table::class => function(Container \$dic) {
        \$dic->service('UserPack\User')->verification();

        return [
            \$dic->model('{$this->pack['pack_name']}\\{$this->entityName}Model'),
        ];
    },
    Form::class => function(Container \$dic) {
        \$dic->service('UserPack\User')->verification();

        return [
            \$dic->model('{$this->pack['pack_name']}\\{$this->entityName}Model'),
            \$dic->form('{$this->pack['pack_name']}\\{$this->entityName}Form')
        ];
    },
    Actions::class => function(Container \$dic) {
        \$dic->service('UserPack\User')->verification();

        return [
            \$dic->model('{$this->pack['pack_name']}\\{$this->entityName}Model')
        ];
    },
];
EOT);
    }

    public function generateControllers(): void
    {
        $controllerDir = $this->packDir . '/Controller';
        $this->createDir($controllerDir);

        $controllerfile = "$controllerDir/Actions.php";

        if(file_exists($controllerfile)) {
            $controllerfile = "$controllerDir/{$this->entityName}Actions.php";
        }

        $this->createFile($controllerfile, <<<EOT
<?php declare(strict_types=1);

namespace {$this->targetPackNamespace}\Controller;

use Prim\{View, AbstractController};

use {$this->targetPackNamespace}\Model\\{$this->entityName}Model;

class Actions extends AbstractController
{
    public function __construct(
        View \$view,
        array \$options,
        public {$this->entityName}Model \${$this->entityName}Model)
    {
        parent::__construct(\$view, \$options);
    }

    public function delete(int \${$this->entityNameLC}Id): void
    {
        \$this->{$this->entityName}Model->delete{$this->entityName}(\${$this->entityNameLC}Id);

        \$this->message('ok', '{$this->entityNameLC} deleted');

        \$this->redirect('/{$this->entityNameLC}s/');
    }
}
EOT);

        $controllerfile = "$controllerDir/Form.php";

        if(file_exists($controllerfile)) {
            $controllerfile = "$controllerDir/{$this->entityName}Form.php";
        }

        $this->createFile($controllerfile, <<<EOT
<?php declare(strict_types=1);

namespace {$this->targetPackNamespace}\Controller;

use Prim\{View, AbstractController};
use Jarzon\ValidationException;

use {$this->targetPackNamespace}\Form\\{$this->entityName}Form;
use {$this->targetPackNamespace}\Model\\{$this->entityName}Model;

class Form extends AbstractController
{
    public function __construct(
    View \$view,
    array \$options,
    protected {$this->entityName}Model \${$this->entityName}Model,
    protected {$this->entityName}Form \${$this->entityName}Form
    ) {
        parent::__construct(\$view, \$options);
    }

    public function add(): void
    {
        if (\$this->{$this->entityName}Form->submitted()) {
            try {
                \$params = \$this->{$this->entityName}Form->validation();
            }
            catch (ValidationException \$e) {
                \$this->addVar('error', \$e->getMessage());
            }

            if(isset(\$params)) {
                \$this->{$this->entityName}Model->add{$this->entityName}(\$params);

                \$this->message('ok', '{$this->entityNameLC} saved');

                \$this->redirect('/{$this->entityNameLC}s/');
            }
        }

        \$this->render('form', '{$this->entityName}Pack', [
            'form' => \$this->{$this->entityName}Form->getForm(),
            '{$this->entityNameLC}' => new class{},
            'new' => true
        ]);
    }

    public function edit(int \${$this->entityNameLC}_id): void
    {
        \$infos = \$this->{$this->entityName}Model->get{$this->entityName}(\${$this->entityNameLC}_id);

        \$this->{$this->entityName}Form->updateValues(\$infos);

        if (\$this->{$this->entityName}Form->submitted()) {

            try {
                \$params = \$this->{$this->entityName}Form->validation();

                \$this->message('ok', '{$this->entityNameLC} updated');
            } catch (ValidationException \$e) {
                \$this->message('error', \$e->getMessage());
            }

            if (isset(\$params)) {
                \$this->{$this->entityName}Model->update{$this->entityName}(\$params, \${$this->entityNameLC}_id);
            }
        }

        \$this->render('form', '{$this->entityName}Pack', [
            'form' => \$this->{$this->entityName}Form->getForm(),
            '{$this->entityNameLC}' => \$infos,
            'new' => false
        ]);
    }
}

EOT);

        $file = <<<EOT
<?php declare(strict_types=1);

namespace {$this->targetPackNamespace}\Controller;

use Prim\{View, AbstractController};
use PaginationPack\Service\Pagination;

use {$this->options['project_name']}\TablePack\Service\Table as TableService;
use {$this->targetPackNamespace}\Entity\\{$this->entityName}Entity;
use {$this->targetPackNamespace}\Model\\{$this->entityName}Model;

class Table extends AbstractController
{
    public function __construct(
        View \$view,
        array \$options,
        public {$this->entityName}Model \${$this->entityName}Model
    ) {
        parent::__construct(\$view, \$options);
    }

    public function index(int \$page = 1): void
    {
        \$paginator = new Pagination(\$page, \$this->{$this->entityName}Model->getNumberOf{$this->entityName}s(), 13, 3);

        \$t = new {$this->entityName}Entity();

        \$table = new TableService('table');

        \$table->setTableClass('table', 'responsiveTable')

EOT;

        foreach ($this->data as $row) {
            if(!$row['public']) continue;

            $file .= "            ->th('{$row['name']}')->order(\$t->{$row['name']})->escape()\n";
        }

        $file .= <<<EOT
            ->th('actions')->colspan(1)
            ->addAction('modify', '/{$this->entityNameLC}s/edit/');

        \${$this->entityNameLC}s = \$this->{$this->entityName}Model->get{$this->entityName}s(\$paginator->getFirstPageElement(), \$paginator->getElementsPerPages(), \$table->getOrderColumn(), \$table->getOrder(), \$table->getOrderColumns());

        \$table->rows(\${$this->entityNameLC}s);

        \$this->render('index', '{$this->entityName}Pack', [
            'paginator' => \$paginator,
            'table' => \$table
        ]);
    }
}

EOT;

        $controllerfile = "$controllerDir/Table.php";

        if(file_exists($controllerfile)) {
            $controllerfile = "$controllerDir/{$this->entityName}Table.php";
        }

        $this->createFile($controllerfile, $file);
    }

    public function generateModel(): void
    {
        $modeDir = "{$this->packDir}/Model";
        $this->createDir($modeDir);

        $file = <<<EOT
<?php declare(strict_types=1);

namespace {$this->targetPackNamespace}\Model;

use Jarzon\QueryBuilder\Builder as QB;
use {$this->targetPackNamespace}\Entity\\{$this->entityName}Entity;
use \PrimPack\Service\PDO;
use Prim\Model;
use {$this->options['project_name']}\UserPack\Service\User;

class {$this->entityName}Model extends Model
{
    public function __construct(
        PDO \$db,
        array \$options,
        public User \$user
    ) {
        parent::__construct(\$db, \$options);
    }

    public function add{$this->entityName}(array \$data): int
    {
        \$m = new {$this->entityName}Entity();

        \$query = QB::insert(\$m)
            ->columns(\$data)
            ->addColumn(\$m->user_id, \$this->user->id);

        return \$query->exec();
    }

    public function update{$this->entityName}(array \$data, int \${$this->entityNameLC}_id): int
    {
        \$m = new {$this->entityName}Entity();

        \$query = QB::update(\$m)
            ->columns(\$data)
            ->where(\$m->id, '=', \${$this->entityNameLC}_id)
            ->where(\$m->user_id, '=', \$this->user->id);

        return \$query->exec();
    }

    public function delete{$this->entityName}(int \$id): int
    {
        return \$this->update{$this->entityName}(['status' => -1], \$id);
    }

    public function get{$this->entityName}(int \${$this->entityNameLC}_id): object|false
    {
        \$m = new {$this->entityName}Entity();

        \$query = QB::select(\$m)
            ->columns(
EOT;

        $select = [];

        foreach ($this->data as $row) {
            if(!$row['public']) continue;

            $select[] = "\$m->{$row['name']}";
        }

        $file .= implode(', ', $select);

        $file .= <<<EOT
)
            ->where(\$m->id, '=', \${$this->entityNameLC}_id)
            ->where(\$m->user_id, '=', \$this->user->id);

        return \$query->fetch();
    }

    public function getNumberOf{$this->entityName}s(): int
    {
        \$m = new {$this->entityName}Entity();

        \$query = QB::select(\$m)
            ->columns(\$m->id->count()->alias('number'))
            ->where(\$m->user_id, '=', \$this->user->id)
            ->whereRaw(\$m->status, '>=', 0);

        return (int)\$query->fetchColumn();
    }

    public function get{$this->entityName}s(int \$mtart, int \$numberOfElements, string \$orderField, string \$order, array \$columns): array|false
    {
        \$m = new {$this->entityName}Entity();

        \$query = QB::select(\$m)
            ->columns(
EOT;

        $file .= implode(', ', $select);

        $file .= <<<EOT
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

        $this->createFile("{$modeDir}/{$this->entityName}Model.php", $file);
    }

    public function generateViews(): void
    {
        $modeDir = $this->packDir . '/view';
        $this->createDir($modeDir);

        $viewFile = "$modeDir/form.php";

        if(file_exists($viewFile) || file_exists("{$modeDir}/index.php")) {
            $modeDir = "{$this->packDir}/view/{$this->entityNameLC}";
            $this->createDir($modeDir);
        }

        // TODO: based $this->data generate the columns
        $file = <<<EOT
<?php declare(strict_types=1);
/**
 * @var \Prim\View \$this
 * @var callable \$_
 * @var callable \$e
 * @var \Libellum\BasePack\Service\ActionsMenu \$_actionMenu
 * @var \Libellum\UserPack\Service\User \$user
 * @var {$this->targetPackNamespace}\Entity\\{$this->entityName}Entity \${$this->entityNameLC}
 */

    if(!\$new && \${$this->entityNameLC}->status === 0) {
        \$_actionMenu
            ->addSubAction(\$_('delete the %s', 'the {$this->entityNameLC}'), 'delete', "/{$this->entityNameLC}s/delete/\${$this->entityNameLC}->id")
            ->confirmation(\$_('are you sure you want to delete %s?', 'this {$this->entityNameLC}'));
    }

\$this->start('default'); ?>
    <div class="box">
        <form action="/{$this->entityNameLC}s/<?=(\$new)? 'create': "edit/\${$this->entityNameLC}->id"?>" method="POST">
            
EOT;

        foreach ($this->data as $row) {
            if(!$row['public']) continue;

            $file .= "<div class=\"listForm\"><?=\$form('{$row['name']}')->label(\$_('{$row['name']}'))->row?></div>
            ";
        }

        $file .= <<<EOT

            <?=\$form('submit')->value(\$_('save {$this->entityNameLC}'))->row?>
            <a class="cancel_button" href="<?=cancel('/{$this->entityNameLC}s/')?>"><?=\$_("cancel")?></a>
        </form>
    </div>
<?php \$this->end() ?>

<?php \$this->start('js') ?>
    <script>
        window.addEventListener('load', function (e) {
            let dates = new DatePicker('input[type="date"]', '<?=\$getLanguage()?>');
        });
    </script>
<?php \$this->end() ?>

EOT;

        $this->createFile($viewFile, $file);

        $this->createFile("{$modeDir}/index.php", <<<EOT
<?php declare(strict_types=1);
\$title = \$_('{$this->entityNameLC}s');

\$this->start('default');
?>
    <a class="buttonLink add" href="/{$this->entityNameLC}s/create"><?=\$_("add new {$this->entityNameLC}")?></a>

    <?php \$this->insert('table', 'TablePack', ['empty' => "you don't have any {$this->entityNameLC}s", 'type' => '{$this->entityNameLC}s']) ?>

    <?php \$this->insert('sections/pagination', 'BasePack') ?>
<?php \$this->end() ?>

EOT);
    }

    public function createDir(string $location): void
    {
        if(!is_dir($location)) {
            if(!mkdir($location)) {
                throw new \Exception('Missing permission to create directory');
            }
        }
    }

    public function createFile(string $location, string $content): void
    {
        if(file_exists($location)) {
            throw new \Exception("File $location already exists");
        }

        file_put_contents($location, $content);
    }
}
