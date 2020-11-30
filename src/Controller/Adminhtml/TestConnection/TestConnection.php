<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Phrase;
use Omikron\Factfinder\Api\Config\AuthConfigInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Credentials;
use Omikron\FactFinder\Communication\Exception\ResponseException;
use Omikron\FactFinder\Communication\Resource\Builder;
use Omikron\Factfinder\Model\Api\CredentialsFactory;

class TestConnection extends Action
{
    /** @var string */
    private $obscuredValue = '******';

    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    /** @var AuthConfigInterface */
    private $authConfig;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var Builder  */
    private $builder;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonResultFactory,
        CredentialsFactory $credentialsFactory,
        AuthConfigInterface $authConfig,
        CommunicationConfigInterface $communicationConfig,
        Builder $builder
    ) {
        parent::__construct($context);
        $this->jsonResultFactory   = $jsonResultFactory;
        $this->credentialsFactory  = $credentialsFactory;
        $this->authConfig          = $authConfig;
        $this->communicationConfig = $communicationConfig;
        $this->builder             = $builder;
    }

    public function execute()
    {
        $message = new Phrase('Connection successfully established.');

        try {
            $request   = $this->getRequest();
            $serverUrl = $request->getParam('address', $this->communicationConfig->getAddress());

            $resource = $this->builder
                ->withCredentials($this->getCredentials($this->getRequest()->getParams()))
                ->withApiVersion($request->getParam('version'))
                ->withServerUrl($serverUrl)
                ->build();

            $resource->search('Search.ff', $request->getParam('channel'));
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
