<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Api;

use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientException;
use Omikron\FactFinder\Communication\Resource\AdapterFactory;
use Omikron\FactFinder\Communication\Version;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\Config\ExportConfig;
use Psr\Log\LoggerInterface;

class PushImport
{
    private CommunicationConfig $communicationConfig;
    private CredentialsFactory $credentialsFactory;
    private ExportConfig $exportConfig;
    private LoggerInterface $logger;
    private ClientBuilder $clientBuilder;
    private string $pushImportResult;

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

        if ($this->communicationConfig->getVersion() === Version::NG && $importAdapter->running($channel)) {
            throw new ClientException("Can't start a new import process. Another one is still going");
        }

        $responses = [];
        foreach ($dataTypes as $dataType) {
            $responses = [...$responses, ...$importAdapter->import($channel, $dataType)];
        }

        $this->pushImportResult = $this->prepareListFromPushImportResponses($responses);

        return true;
    }

    public function getPushImportResult(): string
    {
        return $this->pushImportResult;
    }

    private function prepareListFromPushImportResponses(array $responses): string
    {
        return strtolower($this->communicationConfig->getVersion()) === 'ng' ? $this->ngResponse($responses) : $this->standardResponse($responses);
    }

    private function ngResponse(array $responses): string
    {
        $list = '<ul>%s</ul>';
        $listContent = '';

        foreach ($responses as $response) {
            $importType = sprintf('<li><b>%s push import type</b></li>', $response['importType']);

            $statusMessagesList = sprintf('<ul>%s</ul>', implode('', array_map(function ($message) {
                return sprintf('<li>%s</li>', $message);
            }, $response['statusMessages'])));
            $statusMessages = sprintf('<li><i>Status messages</i></li><li>%s</li>', $statusMessagesList);

            $importType .= $statusMessages;
            $listContent .= $importType;
        }

        return sprintf($list, $listContent);
    }

    private function standardResponse(array $responses): string
    {
        $list = '<ul>%s</ul>';
        $listContent = '';

        if (!empty($responses['status'])) {
            $statusMessagesList = sprintf('<ul>%s</ul>', implode('', array_map(function ($message) {
                return sprintf('<li>%s</li>', $message);
            }, $responses['status'])));

            $statusMessages = sprintf('<li><i>Status messages</i></li><li>%s</li>', $statusMessagesList);
            $listContent .= $statusMessages;
        }

        return sprintf($list, $listContent);
    }
}
