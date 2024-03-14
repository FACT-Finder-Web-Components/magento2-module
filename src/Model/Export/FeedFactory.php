<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;
use Omikron\Factfinder\Api\Export\FieldProviderInterface;

class FeedFactory
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly ObjectManagerInterface $objectManager,
        private readonly array $feedPool
    ) {
    }

    public function create(string $type, array $data = []): Feed
    {
        if (!isset($this->feedPool[$type])) {
            throw new InvalidArgumentException(sprintf('There is no feed configuration for the given type: %s', $type));
        }

        $fieldProvider = $this->feedPool[$type]['fieldProvider'];
        $fields = is_array($fieldProvider)
            ? $fieldProvider
            : call_user_func( //@phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                fn (FieldProviderInterface $fieldProvider) : array => $fieldProvider->getFields() + $fieldProvider->getVariantFields(),
                $this->objectManager->create($fieldProvider)
            );

        $dataProvider = $this->objectManager->create($this->feedPool[$type]['dataProvider'], ['fields' => $fields, 'data' => $data]);

        return $this->objectManager->create($this->feedPool[$type]['generator'], ['dataProvider' => $dataProvider, 'fields' => $fields]); // phpcs:ignore
    }
}
