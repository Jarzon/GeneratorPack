<?php declare(strict_types=1);

namespace GeneratorPack\Service;

use Prim\View;

class File
{
    public array $data;

    public string $packDir;
    public string $packName;
    public string $entityName;
    public string $entityNameLC;
    public string $targetPackNamespace;
    public bool $createCRUD;

    public function __construct(
        public array $options,
        public View $view
    ) {
        $this->options = $options += [
            'project_name' => '',
        ];
    }

    public function setPack(string $packName): void
    {
        if(!str_contains($packName, 'Pack')) {
            $packName = $packName . 'Pack';
        }
        $packName = ucfirst($packName);

        $this->packDir = $this->options['root'] . 'src/' . $packName;
        $this->packName = $packName;
    }

    public function setEntity(string $entityName, bool $crud): void
    {
        if(str_contains($entityName, 'Pack')) {
            $entityName = str_replace('Pack', '', $entityName);
        }

        $this->entityName = ucfirst($entityName);
        $this->entityNameLC = lcfirst($entityName);

        $this->targetPackNamespace = "{$this->options['project_name']}\\{$this->packName}";
        $this->packDir = $this->options['root'] . 'src/' . $this->packName;
        $this->createCRUD = $crud;
    }

    public function savePackStruct(): void
    {
        file_put_contents("{$this->packDir}/config/packStruct.php", serialize(['entity' => $this->entityName, 'lines' => $this->data]));
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

        if($this->createCRUD) {
            $this->generateRouting();
            $this->generateServices();
            $this->generateControllers();
            $this->generateViews();
        }

        $this->savePackStruct();
    }

    public function getTableColumnType(string $type, bool $isType = false): string
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

    public function getColumnType(string $type): string
    {
        if (in_array($type, ['string', 'text', 'textarea', 'email', 'url', 'file', 'tel', 'hidden', 'color', 'password', 'datetime', 'date', 'time', 'currency'])) {
            $type = 'string';
        }
        else if (in_array($type, ['number', 'range'])) {
            $type = 'int';
        }

        return $type;
    }

    public function generateTableEntity(): void
    {
        $entityDir = $this->packDir . '/Entity';

        $this->createDir($entityDir);

        $file = $this->view->fetch('codeTemplates/entityTable', 'GeneratorPack', [
            'file' => $this,
        ]);

        $this->createFile("$entityDir/{$this->entityName}Table.php", $file);
    }

    public function generateEntity(): void
    {
        $entityDir = $this->packDir . '/Entity';

        $this->createDir($entityDir);

        $file = $this->view->fetch('codeTemplates/entity', 'GeneratorPack', [
            'file' => $this,
        ]);

        $this->createFile("$entityDir/{$this->entityName}.php", $file);
    }

    public function generatePhinx(): void
    {
        $phinxDir = $this->packDir . '/migrations/';
        $this->createDir($phinxDir);

        $file = $this->view->fetch('codeTemplates/phinxMigration', 'GeneratorPack', [
            'file' => $this,
        ]);

        $this->createFile($phinxDir . date('YmdHis') . "_{$this->entityNameLC}_init.php", $file);
    }

    public function generateForm(): void
    {
        $formDir = $this->packDir . '/Form';

        $file = $this->view->fetch('codeTemplates/formClass', 'GeneratorPack', [
            'file' => $this,
        ]);

        $this->createDir($formDir);
        $this->createFile("$formDir/{$this->entityName}Form.php", $file);
    }

    public function generateFormLine(array $row): string
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

        $file = $this->view->fetch('codeTemplates/config/routing', 'GeneratorPack', [
            'file' => $this,
        ]);

        $this->createFile("$routingFile", $file);
    }

    public function generateServices(): void
    {
        $servicesFile = "$this->packDir/config/services.php";

        if(file_exists($servicesFile)) {
            $servicesFile = "$this->packDir/config/services_$this->entityNameLC.php";
        }

        $file = $this->view->fetch('codeTemplates/config/services', 'GeneratorPack', [
            'file' => $this,
        ]);

        $this->createFile($servicesFile, $file);
    }

    public function generateControllers(): void
    {
        $controllerDir = $this->packDir . '/Controller';
        $this->createDir($controllerDir);

        $controllerFile = "$controllerDir/Actions.php";

        if(file_exists($controllerFile)) {
            $controllerFile = "$controllerDir/{$this->entityName}Actions.php";
        }

        $file = $this->view->fetch('codeTemplates/controller/actions', 'GeneratorPack', [
            'file' => $this,
        ]);

        $this->createFile($controllerFile, $file);

        $controllerFile = "$controllerDir/Form.php";

        if(file_exists($controllerFile)) {
            $controllerFile = "$controllerDir/{$this->entityName}Form.php";
        }

        $file = $this->view->fetch('codeTemplates/controller/form', 'GeneratorPack', [
            'file' => $this,
        ]);

        $this->createFile($controllerFile, $file);

        $file = $this->view->fetch('codeTemplates/controller/table', 'GeneratorPack', [
            'file' => $this,
        ]);

        $controllerFile = "$controllerDir/Table.php";

        if(file_exists($controllerFile)) {
            $controllerFile = "$controllerDir/{$this->entityName}Table.php";
        }

        $this->createFile($controllerFile, $file);
    }

    public function generateModel(): void
    {
        $modeDir = "{$this->packDir}/Model";
        $this->createDir($modeDir);

        $file = $this->view->fetch('codeTemplates/model', 'GeneratorPack', [
            'file' => $this,
        ]);

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

        $file = $this->view->fetch('codeTemplates/view/form', 'GeneratorPack', [
            'file' => $this,
        ]);

        $this->createFile($viewFile, $file);

        $file = $this->view->fetch('codeTemplates/view/index', 'GeneratorPack', [
            'file' => $this,
        ]);

        $this->createFile("{$modeDir}/index.php", $file);
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
