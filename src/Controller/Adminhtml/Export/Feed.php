<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\FactFinder\Communication\Resource\Builder;
use Omikron\Factfinder\Model\Api\PushImport;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\FtpUploader;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Model\Stream\CsvFactory;
use Omikron\Factfinder\Service\FeedFileService;

class Feed extends Action
{
    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var CommunicationConfig */
    private $communicationConfig;

    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var CsvFactory */
    private $csvFactory;

    /** @var FtpUploader */
    private $ftpUploader;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var PushImport */
    private $pushImport;

    /** @var string */
    protected $feedType = 'product';

    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        CommunicationConfig $communicationConfig,
        StoreEmulation $storeEmulation,
        FeedGeneratorFactory $feedGeneratorFactory,
        CsvFactory $csvFactory,
        FtpUploader $ftpUploader,
        PushImport $pushImport,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->jsonResultFactory    = $jsonResultFactory;
        $this->communicationConfig  = $communicationConfig;
        $this->storeEmulation       = $storeEmulation;
        $this->feedGeneratorFactory = $feedGeneratorFactory;
        $this->csvFactory           = $csvFactory;
        $this->ftpUploader          = $ftpUploader;
        $this->pushImport           = $pushImport;
        $this->storeManager         = $storeManager;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();

        try {
            preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $match);
            $storeId = (int) ($match[1] ?? $this->storeManager->getDefaultStoreView()->getId());
            $messages = [];

            $this->storeEmulation->runInStore($storeId, function () use ($storeId, &$messages, $result) {

                if (!$this->communicationConfig->isChannelEnabled()) {
                    $message = sprintf('Integration for the channel `%s` must be enabled to run %s export', $this->communicationConfig->getChannel(), $this->feedType);
                    $result->setData(['message' => $message]);
                    return $result;
                }

                $filename = (new FeedFileService())->getFeedExportFilename($this->feedType, $this->communicationConfig->getChannel());
                $stream   = $this->csvFactory->create(['filename' => "factfinder/{$filename}"]);
                $this->feedGeneratorFactory->create($this->feedType)->generate($stream);
                $exportPath = $stream->getExportPath();
                $messages[] = file_exists($exportPath) ? __(sprintf('<li>Feed file was generated at %s</li>', $exportPath)) : __('<li>Error while creating feed file</li>');

                try{
                    $this->ftpUploader->upload($filename, $stream);
                    $messages[] = __('<li>File was uploaded to the FTP server.</li>');

                    if ($this->communicationConfig->isPushImportEnabled($storeId)) {
                        try{
                            $this->pushImport->execute($storeId);
                            $result = $this->pushImport->getPushImportResult();
                            $messages[] = __('<li>Push import result</li><ul>' . $result . '</ul>');
                        } catch (Exception $exception) {
                            $messages[] = __('<li>Push import failed.</li>');
                        }
                    }
                } catch (\Exception $exception) {
                    $messages[] = __('<li>Error while uploading file to the FTP.</li><li>Push import was not started.</li>');
                }
            });

            $message = sprintf('<ul>%s</ul>', implode('', $messages));

            $result->setData(['message' => $message]);
        } catch (\Exception $e) {
            $result->setData(['message' => $e->getMessage()]);
        }

        return $result;
    }
}
