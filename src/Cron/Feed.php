<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Model\Api\ActionFactory;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\FtpUploader;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\CsvFactory;

class Feed
{
    private const PATH_CONFIGURABLE_CRON_IS_ENABLED = 'factfinder/configurable_cron/ff_cron_enabled';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var CsvFactory */
    private $csvFactory;

    /** @var FtpUploader */
    private $ftpUploader;

    /** @var string */
    private $feedType;

    /** @var ActionFactory */
    private $actionFactory;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        FeedGeneratorFactory $feedFactory,
        StoreEmulation $emulation,
        CsvFactory $csvFactory,
        FtpUploader $ftpUploader,
        CommunicationConfigInterface $communicationConfig,
        ActionFactory $actionFactory,
        string $type
    ) {
        $this->scopeConfig          = $scopeConfig;
        $this->storeManager         = $storeManager;
        $this->feedGeneratorFactory = $feedFactory;
        $this->storeEmulation       = $emulation;
        $this->csvFactory           = $csvFactory;
        $this->ftpUploader          = $ftpUploader;
        $this->communicationConfig  = $communicationConfig;
        $this->actionFactory        = $actionFactory;
        $this->feedType             = $type;
    }

    public function execute(): void
    {
        if (!$this->scopeConfig->isSetFlag(self::PATH_CONFIGURABLE_CRON_IS_ENABLED)) {
            return;
        }

        foreach ($this->storeManager->getStores() as $store) {
            $this->storeEmulation->runInStore((int) $store->getId(), function () use ($store) {
                if ($this->communicationConfig->isChannelEnabled((int) $store->getId())) {
                    $filename = "export.{$this->communicationConfig->getChannel((int) $store->getId())}.csv";
                    $stream   = $this->csvFactory->create(['filename' => "factfinder/{$filename}"]);
                    $this->feedGeneratorFactory->create($this->feedType)->generate($stream);
                    $this->ftpUploader->upload($filename, $stream);
                    $this->actionFactory->withApiVersion($this->communicationConfig->getVersion())
                        ->getPushImport()
                        ->execute((int) $store->getId());
                }
            });
        }
    }
}
