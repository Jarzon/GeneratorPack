<?php declare(strict_types=1);

namespace GeneratorPack\Form;

use Jarzon\FormAbstract;

class EntityForm extends FormAbstract
{
    public function __construct()
    {
        parent::__construct();

        $this->build();
    }

    public function build(): void
    {
        $this->form
            ->text('entity_name')
                ->autocomplete('off')
            ->checkbox('crud')
                ->value(true)
            ->submit();
    }
}
