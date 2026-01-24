<?php declare(strict_types=1);

namespace GeneratorPack\Form;

use Jarzon\FormAbstract;

class DataForm extends FormAbstract
{
    public function __construct()
    {
        parent::__construct();

        $this->build();
    }

    public function build(): void
    {
        $this->form
            ->repeat()
            ->text('name[]')

            ->select('type[]')
            ->addOptions([
                'text' => 'text', 'textarea' => 'textarea', 'email' => 'email', 'url' => 'url', 'file' => 'file', 'tel' => 'tel', 'hidden' => 'hidden', 'color' => 'color', 'password' => 'password',
                'number' => 'number', 'float' => 'float', 'range' => 'range', 'currency' => 'currency',
                'datetime' => 'datetime', 'date' => 'date', 'time' => 'time'
             ])

            ->number('min[]')
            ->number('max[]')
            ->text('default[]')

            ->select('public[]')
                ->addOptions([
                    'private' => 'private', 'public' => 'public'
                ])
            ->required()

            ->hidden('status[]')
                ->value(0)
            ;
    }
}
