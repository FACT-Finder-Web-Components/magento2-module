<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export;

use InvalidArgumentException;
use Magento\Framework\ObjectManagerInterface;

class FeedFactory
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var string[] */
    private $feedPool;

    public function __construct(ObjectManagerInterface $objectManager, array $feedPool)
    {
        $this->objectManager = $objectManager;
        $this->feedPool      = $feedPool;
    }

    public function create(string $type): Feed
    {
        if (!isset($this->feedPool[$type])) {
            throw new InvalidArgumentException(sprintf('There is no feed with given type: %s', $type));
        }
        return $this->objectManager->create($this->feedPool[$type]); // phpcs:ignore
    }
}
