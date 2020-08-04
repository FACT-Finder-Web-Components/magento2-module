<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Exception;

class AttributeExportException extends \RuntimeException
{
    public function __construct(string $attribute, string $sku, string $message)
    {
        parent::__construct("Error during export an attribute: $attribute from product: $sku. Error message: $message");
    }
}
