<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\FactFinder\Communication\Resource\Builder;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\Config\ExportConfig;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\FtpUploader;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\CsvFactory;

class Feed extends Action
{
    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var ExportConfig */
    private $exportConfig;

    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var CsvFactory */
    private $csvFactory;

    /** @var FtpUploader */
    private $ftpUploader;

    /** @var CredentialsFactory */
    private $credentialsFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var string */
    protected $feedType = 'product';

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        CommunicationConfigInterface $communicationConfig,
        ExportConfig $exportConfig,
        StoreEmulation $storeEmulation,
        FeedGeneratorFactory $feedGeneratorFactory,
        CsvFactory $csvFactory,
        FtpUploader $ftpUploader,
        CredentialsFactory $credentialsFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->jsonResultFactory    = $jsonResultFactory;
        $this->communicationConfig  = $communicationConfig;
        $this->exportConfig         = $exportConfig;
        $this->storeEmulation       = $storeEmulation;
        $this->feedGeneratorFactory = $feedGeneratorFactory;
        $this->csvFactory           = $csvFactory;
        $this->ftpUploader          = $ftpUploader;
        $this->credentialsFactory   = $credentialsFactory;
        $this->storeManager         = $storeManager;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();

        try {
            preg_match('@/store/([0-9]+)/@', (string)$this->_redirect->getRefererUrl(), $match);
            $storeId = (int)($match[1] ?? $this->storeManager->getDefaultStoreView()->getId());
            $this->storeEmulation->runInStore($storeId, function () use ($storeId) {
                $filename = "export.{$this->communicationConfig->getChannel()}.csv";
                $stream   = $this->csvFactory->create(['filename' => "factfinder/{$filename}"]);
                $this->feedGeneratorFactory->create($this->feedType)->generate($stream);
                $this->ftpUploader->upload($filename, $stream);

                $api = (new Builder())
                    ->withApiVersion($this->communicationConfig->getVersion())
                    ->withServerUrl($this->communicationConfig->getAddress())
                    ->withCredentials($this->credentialsFactory->create())
                    ->build();

                foreach ($this->exportConfig->getPushImportDataTypes($storeId) as $dataType) {
                    $api->import($dataType, $this->communicationConfig->getChannel($storeId));
                }
            });

            $result->setData(['message' => __('Feed successfully generated')]);
        } catch (\Exception $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
