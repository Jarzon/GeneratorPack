<?php declare(strict_types=1);

namespace GeneratorPack\Form;

use Jarzon\FormAbstract;

class PackForm extends FormAbstract
{
    public function __construct()
    {
        parent::__construct();

        $this->build();
    }

    public function build(): void
    {
        $this->form
            ->text('pack_name')
            ->text('entity_name')
            ->checkbox('crud')->value(true)
            ->submit();
    }
}
