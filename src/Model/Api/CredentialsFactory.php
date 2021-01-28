<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Magento\Framework\ObjectManagerInterface;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;
use Omikron\FactFinder\Communication\Credentials;

class CredentialsFactory
{
    /** @var AuthConfigInterface */
    private $authConfig;

    /** @var ObjectManagerInterface */
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager, AuthConfigInterface $authConfig)
    {
        $this->objectManager = $objectManager;
        $this->authConfig    = $authConfig;
    }

    public function create(array $authData = null)
    {
        return $this->objectManager->create(Credentials::class, $authData ??
              [
                'username' => $this->authConfig->getUsername(),
                'password' => $this->authConfig->getPassword(),
                'prefix'   => $this->authConfig->getAuthenticationPrefix(),
                'postfix'  => $this->authConfig->getAuthenticationPostfix()
              ]);
    }
}
