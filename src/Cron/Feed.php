<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\StreamInterfaceFactory;
use Omikron\Factfinder\Model\Api\PushImport;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\FtpUploader;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Service\FeedFileService;

class Feed
{
    private const PATH_CONFIGURABLE_CRON_IS_ENABLED = 'factfinder/configurable_cron/ff_cron_enabled';

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly ScopeConfigInterface   $scopeConfig,
        private readonly StoreManagerInterface  $storeManager,
        private readonly FeedGeneratorFactory   $feedGeneratorFactory,
        private readonly StoreEmulation         $storeEmulation,
        private readonly StreamInterfaceFactory $streamFactory,
        private readonly FtpUploader            $ftpUploader,
        private readonly CommunicationConfig    $communicationConfig,
        private readonly PushImport             $pushImport,
        private readonly FeedFileService        $feedFileService,
        private readonly string                 $feedType,
    ) {
    }

    public function execute(): void
    {
        if (!$this->scopeConfig->isSetFlag(self::PATH_CONFIGURABLE_CRON_IS_ENABLED)) {
            return;
        }

        foreach ($this->storeManager->getStores() as $store) {
            $this->storeEmulation->runInStore((int) $store->getId(), function () use ($store) {
                $storeId = (int) $store->getId();
                if ($this->communicationConfig->isChannelEnabled($storeId)) {
                    $filename = $this->feedFileService->getFeedExportFilename(
                        $this->feedType,
                        $this->communicationConfig->getChannel()
                    );
                    $stream   = $this->streamFactory->create(['filename' => "factfinder/{$filename}"]);
                    $this->feedGeneratorFactory->create($this->feedType)->generate($stream);
                    $this->ftpUploader->upload($filename, $stream);
                    if ($this->communicationConfig->isPushImportEnabled($storeId)) {
                        $this->pushImport->execute($storeId);
                    }
                }
            });
        }
    }
}
