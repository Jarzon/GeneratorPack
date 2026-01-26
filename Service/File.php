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

    /** @param array<mixed> $options */
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
        $this->targetPackNamespace = "{$this->options['project_name']}\\{$packName}";
    }

    public function setEntity(string $entityName, bool $crud): void
    {
        if(str_contains($entityName, 'Pack')) {
            $entityName = str_replace('Pack', '', $entityName);
        }

        $this->entityName = ucfirst($entityName);
        $this->entityNameLC = lcfirst($entityName);

        $this->packDir = $this->options['root'] . 'src/' . $this->packName;
        $this->createCRUD = $crud;
    }

    /** @return array<mixed>|false */
    public function getEntityStruct(string $packName, string $entityName): array|false
    {
        if($data = $this->getPackStruct($packName)) {
            if(isset($data[$entityName])) {
                return $data[$entityName];
            }
        }

        return false;
    }

    /** @return array<mixed>|false */
    public function getPackStruct(string $packName): array|false
    {
        try {
            $data = file_get_contents("{$this->options['root']}src/{$packName}/config/packStruct.php");
            return unserialize($data);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function savePackStruct(): void
    {
        $data = $this->getPackStruct($this->packName) ?: [];
        $data[$this->entityName] = [
            'crud' => $this->createCRUD,
            'lines' => $this->getData()
        ];
        file_put_contents("{$this->packDir}/config/packStruct.php", serialize($data));
    }

    /** @return array<mixed> */
    public function getData(): array
    {
        $data = $this->data;
        foreach ($data as $i => $value) {
            if($value['status'] === '-1') unset($data[$i]);
            else unset($data[$i]['status']);
        }
        return $data;
    }

    /** @param array<mixed> $dataValues */
    public function setData(array $dataValues): void
    {
        $this->data = $dataValues;
    }

    public function createPack(): void
    {
        $this->createDir($this->packDir);
    }

    public function createEntity(): void
    {
        $this->createDir($this->packDir);

        $this->createConfigDir();

        $this->createAssetsFolders();
        $this->generateTableEntity(true);
        $this->generateEntity(true);
        $this->generatePhinx(true);
        $this->generateForm(true);
        $this->generateModel();

        if($this->createCRUD) {
            $this->generateTranslationMessages();
            $this->generateRouting();
            $this->generateServices();
            $this->generateControllers();
            $this->generateViews();
        }

        $this->savePackStruct();
    }

    public function updateEntity(): array
    {
        $this->generateTableEntity(true);
        $this->generatePhinx();

        $array = [
            'entity' => $this->generateEntity(),
            'form class' => $this->generateForm(),
            'form view' => $this->createCRUD? $this->generateFormView() : ''
        ];

        $this->savePackStruct();
        return $array;
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

    public function createAssetsFolders(): void
    {
        $dir = $this->packDir . '/assets';

        $this->createDir($dir);

        $this->createDir("$dir/js");
        $this->createDir("$dir/css");
        $this->createDir("$dir/img");
    }

    public function generateTranslationMessages(bool $overwrite = false): void
    {
        $file = $this->view->fetch('codeTemplates/config/messages', 'GeneratorPack', [
            'file' => $this,
        ]);

        $this->createFile("$this->packDir/config/messages.json", $file, $overwrite);
    }

    public function generateTableEntity(bool $overwrite = false): void
    {
        $entityDir = $this->packDir . '/Entity';

        $this->createDir($entityDir);

        $file = $this->view->fetch('codeTemplates/entityTable', 'GeneratorPack', [
            'file' => $this,
        ]);

        $this->createFile("$entityDir/{$this->entityName}Table.php", $file, $overwrite);
    }

    public function generateEntity(bool $isNew = false): string|null
    {
        $entityDir = $this->packDir . '/Entity';

        $this->createDir($entityDir);

        $file = $this->view->fetch('codeTemplates/entity', 'GeneratorPack', [
            'file' => $this,
            'isNew' => $isNew,
        ]);

        if(!$isNew) return $file;

        $this->createFile("$entityDir/{$this->entityName}.php", $file);
        return null;
    }

    public function generatePhinx(bool $isNew = false): void
    {
        $phinxDir = $this->packDir . '/migrations/';
        $this->createDir($phinxDir);

        $file = $this->view->fetch('codeTemplates/phinxMigration', 'GeneratorPack', [
            'file' => $this,
            'isNew' => $isNew,
        ]);

        $this->createFile($phinxDir . date('YmdHis') . "_{$this->entityNameLC}_".($isNew? 'init' : 'update').".php", $file);
    }

    public function generateForm(bool $isNew = false): string|null
    {
        $formDir = $this->packDir . '/Form';

        $file = $this->view->fetch('codeTemplates/formClass', 'GeneratorPack', [
            'file' => $this,
        ]);

        if(!$isNew) {
            return $file;
        }

        $this->createDir($formDir);
        $this->createFile("$formDir/{$this->entityName}Form.php", $file);
        return null;
    }

    /** @param array<mixed> $row */
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

        $file = $this->generateFormView(true);

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

    public function createFile(string $location, string $content, bool $overwrite = false): void
    {
        if(!$overwrite && file_exists($location)) {
            throw new \Exception("File $location already exists");
        }

        file_put_contents($location, $content);
    }

    public function createConfigDir(): void
    {
        $configDir = $this->packDir . '/config';
        $this->createDir($configDir);
    }

    public function generateFormView(bool $isNew = false): string
    {
        $file = $this->view->fetch('codeTemplates/view/form', 'GeneratorPack', [
            'file' => $this,
            'isNew' => $isNew
        ]);
        return $file;
    }

    function getEntityTableName(): string
    {
        static $cache = "{$this->entityName}Table";

        return $cache;
    }
}
