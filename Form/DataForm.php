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

            ->select('value[]')
            ->addOptions([
                'string' => 'string',
                'text' => 'text',
                'date' => 'date',
                'datetime' => 'datetime',
                'time' => 'time',
                'decimal' => 'decimal',
                'smallinteger' => 'smallinteger',
                'integer' => 'integer',
                'biginteger' => 'biginteger',
            ])

            ->select('input[]')
            ->addOptions([
                '' => '',
                'text' => 'text',
                'textarea' => 'textarea',
                'select' => 'select',
                'radio' => 'radio',
                'checkbox' => 'checkbox',
                'email' => 'email',
                'url' => 'url',
                'file' => 'file',
                'tel' => 'tel',
                'hidden' => 'hidden',
                'color' => 'color',
                'password' => 'password',
                'number' => 'number',
                'float' => 'float',
                'range' => 'range',
                'currency' => 'currency',
                'datetime' => 'datetime',
                'date' => 'date',
                'time' => 'time'
             ])

            ->number('min[]')
            ->number('max[]')
            ->text('default[]')

            ->radio('public[]')
                ->addOptions([
                    'private' => 'private',
                    'public' => 'public'
                ])
                ->value('private')
            ->required()

            ->hidden('status[]')
                ->value(0)

            ->submit();
    }
}
