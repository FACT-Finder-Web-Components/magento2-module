<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Framework\ObjectManagerInterface;
use Omikron\Factfinder\Api\FeedServiceInterface;

class FeedServiceFactory
{
    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var string[] */
    private $servicesPool;

    public function __construct(ObjectManagerInterface $objectManager, array $servicesPool)
    {
        $this->objectManager = $objectManager;
        $this->servicesPool  = $servicesPool;
    }

    public function create(string $type): FeedServiceInterface
    {
        if (!isset($this->servicesPool[$type])) {
            throw new \InvalidArgumentException('Invalid feed type');
        }

        return $this->objectManager->create($this->servicesPool[$type]); // phpcs:ignore
    }
}
