<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Catalog\ProductField;

use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Magento\Framework\ObjectManagerInterface;
use Omikron\Factfinder\Api\Export\Catalog\ProductFieldInterface;
use Omikron\Factfinder\Exception\AttributeExportException;
use Psr\Log\LoggerInterface;

class GenericField implements ProductFieldInterface
{
    /** @var string */
    private $attributeName;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        string $attributeName,
        ScopeConfigInterface $scopeConfig,
        ObjectManagerInterface $objectManager
    ) {
        $this->attributeName = $attributeName;
        $this->scopeConfig   = $scopeConfig;
        $this->logger        = $objectManager->get('Omikron\Factfinder\Export\Logger');
    }

    /**
     * @param Product $product
     *
     * @return string
     * @throws AttributeExportException
     */
    public function getValue(Product $product): string
    {
        try {
            return (string) $product->getAttributeText($this->attributeName);
        } catch (\Throwable $e) {
            if ($this->scopeConfig->isSetFlag('factfinder/general/logging_enabled')) {
                $this->logger->error(new Phrase(
                    'Error during export an attribute: %1 from product %2. Error message: %3',
                    [$this->attributeName, $product->getSku(), $e->getMessage()]
                ));
            }
            throw new AttributeExportException($this->attributeName, $product->getSku(), $e->getMessage());
        }
    }
}
