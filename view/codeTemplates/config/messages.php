<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

echo <<<EOT
{
  "$file->entityName": ["$file->entityName", "$file->entityName"],
  "{$file->entityName}s": ["{$file->entityName}s", "{$file->entityName}s"],
  "a $file->entityName": ["a $file->entityName", "un/une $file->entityName"],
  "the $file->entityName": ["the $file->entityName", "le/la $file->entityName"],
  "this $file->entityName": ["this $file->entityName", "cet/cette $file->entityName"],
}
EOT;
