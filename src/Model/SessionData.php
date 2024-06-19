<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class SessionData implements SectionSourceInterface, ParametersSourceInterface
{
    public function __construct(private readonly CustomerSession $customerSession)
    {
    }

    public function getUserId(): string
    {
        return (string) $this->customerSession->getCustomerId();
    }

    /**
     * @return array
     */
    public function getSectionData()
    {
        return [
            'uid'      => $this->getUserId(),
            'internal' => false,
        ];
    }

    public function getParameters(): array
    {
        return ['user-id' => $this->getUserId() ?: null];
    }
}
