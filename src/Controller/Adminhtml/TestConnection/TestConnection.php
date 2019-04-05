<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\Api\TestConnection as ApiConnectionTest;

class TestConnection extends Action
{
    /** @var string */
    private $obscuredValue = '******';

    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    /** @var ApiConnectionTest */
    private $testConnection;

    /** @var AuthConfigInterface */
    private $authConfig;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonResultFactory,
        CredentialsFactory $credentialsFactory,
        AuthConfigInterface $authConfig,
        ApiConnectionTest $testConnection
    ) {
        parent::__construct($context);
        $this->jsonResultFactory  = $jsonResultFactory;
        $this->credentialsFactory = $credentialsFactory;
        $this->testConnection     = $testConnection;
        $this->authConfig         = $authConfig;
    }

    public function execute()
    {
        $message = __('Connection successfully established.');

        try {
            $request = $this->getRequest();
            $params  = $this->getCredentials($request->getParams()) + ['channel' => $request->getParam('channel')];
            $this->testConnection->execute($request->getParam('address'), $params);
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        return $this->jsonResultFactory->create()->setData(['message' => $message]);
    }

    private function getCredentials(array $params): array
    {
        // The password wasn't edited, load it from config
        if ($params['password'] === $this->obscuredValue) {
            $params['password'] = $this->authConfig->getPassword();
        }

        $params += [
            'prefix'  => $params['authentication_prefix'],
            'postfix' => $params['authentication_postfix'],
        ];
        return $this->credentialsFactory->create($params)->toArray();
    }
}
