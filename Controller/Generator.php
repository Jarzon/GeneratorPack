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
        $root = $this->options['root'] . 'src/';

        $packs = glob("$root*");

        if($packs === false) {
            throw new \Exception("Impossible d'accèder au dossier src/");
        }

        foreach ($packs as $index => $dir) {
            $packs[$index] = str_replace($root, '', $dir);
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
        ]);
    }

    public function modify(string $packName, string|null $entityName = null): void
    {
        $this->file->setPack($packName);

        $data = $entityName !== null? $this->file->getEntityStruct($packName, $entityName) : [];

        if($entityName !== null) $this->file->setEntity($entityName, $data['crud'] ?? true);

        if($this->entityForm->submitted()) {
            try {
                $entityValues = $this->entityForm->validation();
                $dataValues = $this->dataForm->validation();
            }
            catch(ValidationException $e) {
                $this->message('alert', $e->getMessage());
            }

            if(!empty($dataValues)) {
                if($entityName === null) $this->file->setEntity($entityValues['entity_name'], $entityValues['crud']);
                $this->file->setData($dataValues);

                if($entityName === null) {
                    $this->file->createEntity();
                    $this->message('ok', 'The entity was successful created');
                    $this->redirect("/admin/generator/modify/$packName/$entityName");
                } else {
                    $newCode = $this->file->updateEntity();
                    $this->message('ok', 'The entity was successful updated');
                }
            }
        }

        $entities = array_keys($this->file->getPackStruct($packName) ?: []);

        $this->render('form', 'GeneratorPack', [
            'entityForm' => $this->entityForm->getForm(),
            'dataForm' => $this->dataForm->getForm(),
            'packName' => $packName,
            'entityName' => $entityName ?: null,
            'entities' => $entities,
            'lines' => $data['lines'] ?? [],
            'newCode' => $newCode ?? []
        ]);
    }
}
