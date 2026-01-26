<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

echo <<<EOT
{
  "$file->entityName": ["$file->entityName in english", "$file->entityName in french"]
}
EOT;
