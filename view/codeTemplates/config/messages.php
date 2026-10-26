<?php
declare(strict_types=1);
/**
 * @var \Prim\View $this
 * @var \GeneratorPack\Service\File $file
 */

$entityName = lcfirst($file->entityName);

echo <<<EOT
{
  "$file->entityName": ["$file->entityName", "$file->entityName"],
  "{$entityName}s": ["{$entityName}s", "{$entityName}s"],
  "a $entityName": ["a $entityName", "un/une $entityName"],
  "the $entityName": ["the $entityName", "le/la $entityName"],
  "this $entityName": ["this $entityName", "cet/cette $entityName"],
  "any $entityName": ["any $entityName", "un/une $entityName"],
}
EOT;
