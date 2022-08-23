<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class SessionData implements SectionSourceInterface, ParametersSourceInterface
{
    /** @var CustomerSession */
    private $customerSession;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var RemoteAddress */
    private $remoteAddress;

    public function __construct(
        CustomerSession $customerSession,
        ScopeConfigInterface $scopeConfig,
        RemoteAddress $remoteAddress
    ) {
        $this->customerSession = $customerSession;
        $this->scopeConfig     = $scopeConfig;
        $this->remoteAddress   = $remoteAddress;
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
            'internal' => $this->isInternal(),
        ];
    }

    public function getParameters(): array
    {
        return ['user-id' => $this->getUserId() ?: null];
    }

    private function isInternal(): bool
    {
        $internalIps = explode(',', (string) $this->scopeConfig->getValue('factfinder/advanced/internal_ips'));
        return in_array($this->remoteAddress->getRemoteAddress(), array_map('trim', $internalIps));
    }
}
