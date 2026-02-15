<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

echo <<<EOT
{
  "$file->entityName": ["$file->entityName in english", "$file->entityName en français"],
  "the $file->entityName": ["the $file->entityName in english", "le/la $file->entityName en français"],
  "this $file->entityName": ["this $file->entityName in english", "cet/cette $file->entityName en français"],
}
EOT;
