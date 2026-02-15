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
use Jarzon\ValidationException;

use {$file->targetPackNamespace}\Form\\{$file->entityName}Form;
use {$file->targetPackNamespace}\Model\\{$file->entityName}Model;

class Form extends AbstractController
{
    public function __construct(
        View \$view,
        array \$options,
        protected {$file->entityName}Model \${$file->entityName}Model,
        protected {$file->entityName}Form \${$file->entityName}Form
    ) {
        parent::__construct(\$view, \$options);
    }

    public function add(): void
    {
        if (\$this->{$file->entityName}Form->submitted()) {
            try {
                \$params = \$this->{$file->entityName}Form->validation();
            }
            catch (ValidationException \$e) {
                \$this->addVar('error', \$e->getMessage());
            }

            if(isset(\$params)) {
                \$this->{$file->entityName}Model->add{$file->entityName}(\$params);

                \$this->message('ok', '%s saved', '{$file->entityNameLC}');

                \$this->redirect('/{$file->tableName}/');
            }
        }

        \$this->render('form', '{$file->entityName}Pack', [
            'form' => \$this->{$file->entityName}Form->getForm(),
            '{$file->entityNameLC}' => new class{},
            'new' => true
        ]);
    }

    public function edit(int \${$file->entityNameLC}_id): void
    {
        \$infos = \$this->{$file->entityName}Model->get{$file->entityName}(\${$file->entityNameLC}_id);

        \$this->{$file->entityName}Form->updateValues(\$infos);

        if (\$this->{$file->entityName}Form->submitted()) {

            try {
                \$params = \$this->{$file->entityName}Form->validation();

                \$this->message('ok', '%s updated', '{$file->entityNameLC}');
            } catch (ValidationException \$e) {
                \$this->message('error', \$e->getMessage());
            }

            if (isset(\$params)) {
                \$this->{$file->entityName}Model->update{$file->entityName}(\$params, \${$file->entityNameLC}_id);
            }
        }

        \$this->render('form', '{$file->entityName}Pack', [
            'form' => \$this->{$file->entityName}Form->getForm(),
            '{$file->entityNameLC}' => \$infos,
            'new' => false
        ]);
    }
}

EOT;
