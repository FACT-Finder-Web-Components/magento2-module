<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientException;
use Omikron\FactFinder\Communication\Resource\AdapterFactory;
use Omikron\Factfinder\Model\Config\ExportConfig;
use Psr\Log\LoggerInterface;

class PushImport
{
    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    /** @var ExportConfig */
    private $exportConfig;

    /** @var LoggerInterface */
    private $logger;

    /** @var ClientBuilder */
    private $clientBuilder;

    public function __construct(
        ClientBuilder $clientBuilder,
        CredentialsFactory $credentialsFactory,
        CommunicationConfigInterface $communicationConfig,
        ExportConfig $exportConfig,
        LoggerInterface $logger
    ) {
        $this->clientBuilder       = $clientBuilder;
        $this->credentialsFactory  = $credentialsFactory;
        $this->communicationConfig = $communicationConfig;
        $this->exportConfig        = $exportConfig;
        $this->logger              = $logger;
    }

    public function execute(int $storeId): bool
    {
        $clientBuilder = $this->clientBuilder
            ->withServerUrl($this->communicationConfig->getAddress())
            ->withCredentials($this->credentialsFactory->create());

        $importAdapter = (new AdapterFactory($clientBuilder, $this->communicationConfig->getVersion()))->getImportAdapter();

        $channel = $this->communicationConfig->getChannel($storeId);
        if ($importAdapter->running($channel)) {
            throw new ClientException("Can't start a new import process. Another one is still going");
        }

        $response = [];
        foreach ($this->exportConfig->getPushImportDataTypes($storeId) as $dataType) {
            $response = array_merge_recursive($response, $importAdapter->import($channel, $dataType));
        }
        return $response && !(isset($response['errors']) || isset($response['error']));
    }
}
