<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

echo <<<EOT
<?php declare(strict_types=1);

namespace {$file->targetPackNamespace}\Form;

use Jarzon\FormAbstract;

class {$file->entityName}Form extends FormAbstract
{

    public function __construct()
    {
        parent::__construct();

        \$this->build();
    }
    
    public function build(): void
    {
        \$this->form

EOT;

foreach ($file->data as $row) {
    if($row['public'] === 'private') continue;

    echo $file->generateFormLine($row);
} ?>
        ->submit();
    }

    public function buildAdmin(): void
    {
        $this->form
<?php
foreach ($file->data as $row) {
    if($row['public'] === 'public') continue;

    echo $file->generateFormLine($row);
} ?>

        ;
    }
}
