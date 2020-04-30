<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;
use Omikron\Factfinder\Api\SessionDataInterface;

class SessionData implements SessionDataInterface, SectionSourceInterface, ParametersSourceInterface
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

    public function getUserId(): int
    {
        return (int) $this->customerSession->getCustomerId();
    }

    public function getSessionId(): string
    {
        return $this->getCorrectSessionId((string) $this->customerSession->getSessionId());
    }

    /**
     * @return array
     */
    public function getSectionData()
    {
        return [
            'uid'      => $this->getUserId(),
            'sid'      => $this->getSessionId(),
            'internal' => $this->isInternal(),
        ];
    }

    public function getParameters(): array
    {
        return [
            'sid'     => $this->getSessionId(),
            'user-id' => $this->getUserId() ?: null,
        ];
    }

    private function getCorrectSessionId(string $sessionId, int $length = 30): string
    {
        $sessionId = $sessionId ?: sha1(uniqid('', true));
        $sessionId = str_repeat($sessionId, intdiv($length, strlen($sessionId)) + 1);
        return substr($sessionId, 0, $length);
    }

    private function isInternal(): bool
    {
        $internalIps = explode(',', (string) $this->scopeConfig->getValue('factfinder/advanced/internal_ips'));
        return in_array($this->remoteAddress->getRemoteAddress(), array_map('trim', $internalIps));
    }
}
