<?php declare(strict_types=1);

namespace GeneratorPack\Controller;

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

    public function create(): void
    {
        if($this->packForm->submitted()) {
            try {
                $packValues = $this->packForm->validation();
                $dataValues = $this->dataForm->validation();
            }
            catch(ValidationException $e) {
                $this->message('alert', $e->getMessage());
            }

            if(!empty($packValues) && !empty($dataValues)) {
                $this->file->setPack($packValues);
                $this->file->setData($dataValues);

                $this->file->createPack();
            }
        }

        $this->render('form', 'GeneratorPack', [
            'packForm' => $this->packForm->getForm(),
            'dataForm' => $this->dataForm->getForm(),
        ]);
    }
}
