<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 * @var bool $isNew
 */

if($isNew) {
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
    
    

EOT;
}
if($isNew): ?>
    public function build(): void
    {
        \$this->form

<?php endif;
foreach ($file->data as $row) {
    if($row['public'] === 'private' || (!$isNew && $row['status'] !== '1' && $row['status'] !== '2')) continue;

    echo $file->generateFormLine($row);
}
if($isNew): ?>
        ->submit();
    }

    public function buildAdmin(): void
    {
        $this->form
<?php
endif;
foreach ($file->data as $row) {
    if($row['public'] === 'public' || (!$isNew && $row['status'] !== '1' && $row['status'] !== '2')) continue;

    echo $file->generateFormLine($row);
}
if($isNew): ?>

        ;
    }
}
<?php endif; ?>