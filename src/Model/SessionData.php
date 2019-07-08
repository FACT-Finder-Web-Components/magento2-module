<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;
use Omikron\Factfinder\Api\SessionDataInterface;

class SessionData implements SessionDataInterface, SectionSourceInterface, ParametersSourceInterface
{
    /** @var CustomerSession */
    protected $customerSession;

    public function __construct(CustomerSession $customerSession) // phpcs:ignore
    {
        $this->customerSession = $customerSession;
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
            'uid' => $this->getUserId(),
            'sid' => $this->getSessionId(),
        ];
    }

    public function getParameters(): array
    {
        return [
            'sid'     => $this->getSessionId(),
            'user-id' => $this->getUserId() ?: null,
        ];
    }

    protected function getCorrectSessionId(string $sessionId, int $length = 30): string
    {
        $sessionId = $sessionId ?: sha1(uniqid('', true));
        $sessionId = str_repeat($sessionId, intdiv($length, strlen($sessionId)) + 1);
        return substr($sessionId, 0, $length);
    }
}
