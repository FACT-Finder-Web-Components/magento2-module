<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Resource\Builder;
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

    public function __construct(
        CredentialsFactory $credentialsFactory,
        CommunicationConfigInterface $communicationConfig,
        ExportConfig $exportConfig,
        LoggerInterface $logger
    ) {
        $this->communicationConfig = $communicationConfig;
        $this->exportConfig        = $exportConfig;
        $this->credentialsFactory  = $credentialsFactory;
        $this->logger              = $logger;
    }

    public function execute(int $storeId)
    {
        $builder = (new Builder())
            ->withApiVersion($this->communicationConfig->getVersion())
            ->withServerUrl($this->communicationConfig->getAddress())
            ->withCredentials($this->credentialsFactory->create());

        if ($this->communicationConfig->isLoggingEnabled()) {
            $builder->withLogger($this->logger);
        }
        $api = $builder->build();

        foreach ($this->exportConfig->getPushImportDataTypes($storeId) as $dataType) {
            $api->import($dataType, $this->communicationConfig->getChannel($storeId));
        }
    }
}
