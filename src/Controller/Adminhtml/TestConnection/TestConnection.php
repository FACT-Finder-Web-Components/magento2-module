<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Phrase;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Api\ActionFactory;
use Omikron\Factfinder\Model\Api\Credentials;
use Omikron\Factfinder\Model\Api\CredentialsFactory;

class TestConnection extends Action
{
    /** @var string */
    private $obscuredValue = '******';

    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    /** @var ActionFactory */
    private $actionFactory;

    /** @var AuthConfigInterface */
    private $authConfig;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonResultFactory,
        CredentialsFactory $credentialsFactory,
        AuthConfigInterface $authConfig,
        CommunicationConfigInterface $communicationConfig,
        ActionFactory $actionFactory
    ) {
        parent::__construct($context);
        $this->jsonResultFactory   = $jsonResultFactory;
        $this->credentialsFactory  = $credentialsFactory;
        $this->actionFactory       = $actionFactory;
        $this->authConfig          = $authConfig;
        $this->communicationConfig = $communicationConfig;
    }

    public function execute()
    {
        $message = new Phrase('Connection successfully established.');

        try {
            $request   = $this->getRequest();
            $params    = ['channel' => $request->getParam('channel')];
            $serverUrl = $request->getParam('address', $this->communicationConfig->getAddress());

            $testConnection = $this->actionFactory
                ->withCredentials($this->getCredentials($request->getParams()))
                ->withApiVersion($request->getParam('version'))
                ->getTestConnection();

            $testConnection->execute($serverUrl, $params);
        } catch (ResponseException $e) {
            $message = $e->getMessage();
        }

        return $this->jsonResultFactory->create()->setData(['message' => $message]);
    }

    private function getCredentials(array $params): Credentials
    {
        // The password wasn't edited, load it from config
        if (!isset($params['password']) || $params['password'] === $this->obscuredValue) {
            $params['password'] = $this->authConfig->getPassword();
        }

        return $this->credentialsFactory->create($params);
    }
}
