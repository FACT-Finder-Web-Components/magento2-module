<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientException;
use Omikron\FactFinder\Communication\Resource\AdapterFactory;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\Config\ExportConfig;
use Psr\Log\LoggerInterface;

class PushImport
{
    /** @var CommunicationConfig */
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
        CommunicationConfig $communicationConfig,
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
        $channel       = $this->communicationConfig->getChannel($storeId);
        $dataTypes     = $this->exportConfig->getPushImportDataTypes($storeId);

        if (!$dataTypes) {
            return false;
        }

        if ($importAdapter->running($channel)) {
            throw new ClientException("Can't start a new import process. Another one is still going");
        }

        foreach ($dataTypes as $dataType) {
            $importAdapter->import($channel, $dataType);
        }

        return true;
    }
}
