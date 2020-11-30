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

    /** @var Builder */
    private $builder;

    public function __construct(
        Builder $builder,
        CredentialsFactory $credentialsFactory,
        CommunicationConfigInterface $communicationConfig,
        ExportConfig $exportConfig,
        LoggerInterface $logger
    ) {
        $this->builder             = $builder;
        $this->credentialsFactory  = $credentialsFactory;
        $this->communicationConfig = $communicationConfig;
        $this->exportConfig        = $exportConfig;
        $this->logger              = $logger;
    }

    public function execute(int $storeId): bool
    {
        $this->builder->withApiVersion($this->communicationConfig->getVersion())
            ->withServerUrl($this->communicationConfig->getAddress())
            ->withCredentials($this->credentialsFactory->create());

        if ($this->communicationConfig->isLoggingEnabled()) {
            $this->builder->withLogger($this->logger);
        }

        $resource = $this->builder->build();
        $response = [];

        foreach ($this->exportConfig->getPushImportDataTypes($storeId) as $dataType) {
            $response = array_merge_recursive($response, $resource->import($dataType, $this->communicationConfig->getChannel($storeId)));
            $resource->import($dataType, $this->communicationConfig->getChannel($storeId));
        }

        return $response && !(isset($response['errors']) || isset($response['error']));
    }
}
