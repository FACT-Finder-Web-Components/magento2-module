<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;
use Omikron\Factfinder\Api\Export\FieldProviderInterface;

class FeedFactory
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var string[] */
    private $feedPool;

    public function __construct(
        ObjectManagerInterface $objectManager,
        array $feedPool
    ) {
        $this->objectManager = $objectManager;
        $this->feedPool      = $feedPool;
    }

    public function create(string $type): Feed
    {
        if (!isset($this->feedPool[$type])) {
            throw new InvalidArgumentException(sprintf('There is no feed configuration for the given type: %s', $type));
        }

        $fields = is_array($this->feedPool[$type]['fieldProvider'])
            ? $this->feedPool[$type]['fieldProvider']
            : $this->objectManager->create($this->feedPool[$type]['fieldProvider'])->getFields();

        $dataProvider = $this->objectManager->create($this->feedPool[$type]['dataProvider'], ['fields' => $fields]);

        return $this->objectManager->create($this->feedPool[$type]['generator'], ['dataProvider' => $dataProvider, 'fields' => $fields]); // phpcs:ignore
    }
}
