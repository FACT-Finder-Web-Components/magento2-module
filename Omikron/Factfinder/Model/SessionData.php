<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Customer\Model\Session as CustomerSession;
use Omikron\Factfinder\Api\SessionDataInterface;

class SessionData implements SessionDataInterface
{
    /** @var CustomerSession */
    private $customerSession;

    public function __construct(CustomerSession $customerSession)
    {
        $this->customerSession = $customerSession;
    }

    public function getUserId(): int
    {
        return (int) $this->customerSession->getCustomerId();
    }

    public function getSessionId(): string
    {
        return $this->getCorrectSessionId($this->customerSession->getSessionId());
    }

    private function getCorrectSessionId(string $sessionId, int $length = 30): string
    {
        $sessionId = $sessionId ?: md5(uniqid('', true));
        $sessionId = str_repeat($sessionId, intdiv($length, strlen($sessionId)) + 1);
        return substr($sessionId, 0, $length);
    }
}
