<?php declare(strict_types=1);

namespace GeneratorPack\Controller;

use GeneratorPack\Form\EntityForm;
use Jarzon\ValidationException;
use GeneratorPack\Service\File;
use GeneratorPack\Form\DataForm;
use GeneratorPack\Form\PackForm;
use Prim\{View, AbstractController};

class Generator extends AbstractController
{
    public function __construct(
        View $view,
        array $options,
        public PackForm $packForm,
        public EntityForm $entityForm,
        public DataForm $dataForm,
        public File $file
    ) {
        parent::__construct($view, $options);
    }

    public function index(): void
    {
        $src = $this->options['root'] . 'src/';

        $packs = glob("$src*");

        if($packs === false) {
            throw new \Exception("Unable to access the src/ directory at: $src");
        }

        foreach ($packs as $index => $dir) {
            $packs[$index] = str_replace($src, '', $dir);
        }

        $this->render('index', 'GeneratorPack', [
            'packs' => $packs
        ]);
    }

    public function createPack(): void
    {
        if($this->packForm->submitted()) {
            try {
                $packValues = $this->packForm->validation();
            }
            catch(ValidationException $e) {
                $this->message('alert', $e->getMessage());
            }

            if(!empty($packValues)) {
                $this->file->setPack($packValues['pack_name']);

                $this->file->createPack();
                $this->message('ok', 'The pack was successfully created');
                $this->redirect("/admin/generator/modify/{$this->file->packName}");
            }
        }

        $this->render('createpack', 'GeneratorPack', [
            'packForm' => $this->packForm->getForm(),
            'isNew' => true
        ]);
    }

    public function modify(string $packName, string|null $entityName = null, int $recreate = 0): void
    {
        $packName = $this->file->setPack($packName);

        $data = $entityName !== null? $this->file->getEntityStruct($packName, $entityName) : [];

        if($data === false) {
            $this->message('error', "The entity <b>$entityName</b> doesnt exist");
            $this->redirect("/admin/generator/modify/$packName");
        }

        if($entityName !== null) $this->file->setEntity($entityName, $data['tableName'], $data['crud'] ?? true);

        $newCode = [];

        if($this->dataForm->submitted()) {
            try {
                if($entityName === null) $entityValues = $this->entityForm->validation();
                $dataValues = $this->dataForm->validation();

                $isChange = false;

                if($recreate === 1) {
                    foreach ($dataValues as $i => $row) {

                        if($row['status'] !== '-1') $dataValues[$i]['status'] = '1';
                    }
                    $isChange = true;
                } else {
                    foreach ($dataValues as $row) {
                        if($row['status'] !== '0') $isChange = true;
                    }
                }

                if(!$isChange) {
                    $this->message('info', 'Nothing to save');
                }
                else if(!empty($dataValues)) {
                    if($entityName === null) $this->file->setEntity($entityValues['entity_name'], $entityValues['table_name'], $entityValues['crud']);
                    $this->file->setData($dataValues);

                    if($entityName === null || $recreate === 1) {
                        if($recreate === 1) $this->file->removeCRUD();
                        $this->file->createEntity($entityValues['disableCodeGeneration'] ?? false);
                        $this->message('ok', 'The entity was successful created');
                        $this->redirect("/admin/generator/modify/$packName/{$this->file->entityName}");
                    } else {
                        $newCode = $this->file->updateEntity();
                        $this->message('ok', 'The entity was successful updated');
                    }
                }
            }
            catch(ValidationException $e) {
                $this->message('alert', $e->getMessage());
            }
        }

        $routingFile = file_get_contents("{$this->options['root']}app/config/routing.php");
        $serviceFile = file_get_contents("{$this->options['root']}app/config/services.php");

        if(!str_contains($routingFile, $packName)) {
            $newCode["app/config/routing.php"] = "->registerRoutes('$packName', 'routing.php')";
        }

        if(!str_contains($serviceFile, $packName)) {
            $newCode["app/config/services.php"] = "->registerServices('$packName')";
        }

        $entities = array_keys($this->file->getPackStruct($packName) ?: []);

        $this->render('form', 'GeneratorPack', [
            'entityForm' => $this->entityForm->getForm(),
            'dataForm' => $this->dataForm->getForm(),
            'packName' => $packName,
            'entityName' => $entityName ?: null,
            'entities' => $entities,
            'lines' => $data['lines'] ?? [],
            'newCode' => $newCode,
            'isNew' => false
        ]);
    }
}
