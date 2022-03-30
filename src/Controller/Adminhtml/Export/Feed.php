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
use Omikron\Factfinder\Api\StreamInterfaceFactory;
use Omikron\Factfinder\Service\FeedFileService;

class Feed extends Action
{
    protected string $feedType = 'product';
    private JsonFactory $jsonResultFactory;
    private CommunicationConfig $communicationConfig;
    private StoreEmulation $storeEmulation;
    private FeedGeneratorFactory $feedGeneratorFactory;
    private StreamInterfaceFactory $streamFactory;
    private FtpUploader $ftpUploader;
    private StoreManagerInterface $storeManager;
    private PushImport $pushImport;

    /**
     * @param Context                $context
     * @param JsonFactory            $jsonResultFactory
     * @param CommunicationConfig    $communicationConfig
     * @param StoreEmulation         $storeEmulation
     * @param FeedGeneratorFactory   $feedGeneratorFactory
     * @param StreamInterfaceFactory $streamFactory
     * @param FtpUploader            $ftpUploader
     * @param PushImport             $pushImport
     * @param StoreManagerInterface  $storeManager
     * @param FeedFileService        $feedFileService
     * @SuppressWarnings(PHPMD)
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonResultFactory,
        CommunicationConfig $communicationConfig,
        StoreEmulation $storeEmulation,
        FeedGeneratorFactory $feedGeneratorFactory,
        StreamInterfaceFactory $streamFactory,
        FtpUploader $ftpUploader,
        PushImport $pushImport,
        StoreManagerInterface $storeManager,
        FeedFileService $feedFileService
    ) {
        parent::__construct($context);
        $this->jsonResultFactory    = $jsonResultFactory;
        $this->communicationConfig  = $communicationConfig;
        $this->storeEmulation       = $storeEmulation;
        $this->feedGeneratorFactory = $feedGeneratorFactory;
        $this->streamFactory        = $streamFactory;
        $this->ftpUploader          = $ftpUploader;
        $this->pushImport           = $pushImport;
        $this->storeManager         = $storeManager;
        $this->feedFileService      = $feedFileService;
    }

    public function execute()
    {
        $result = $this->jsonResultFactory->create();

        try {
            //@phpcs:ignore Magento2.Legacy.ObsoleteResponse.RedirectResponseMethodFound
            preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $match);
            $storeId = (int) ($match[1] ?? $this->storeManager->getDefaultStoreView()->getId());
            $messages = [];

            $this->storeEmulation->runInStore($storeId, function () use ($storeId, &$messages, $result) {

                $channel = $this->communicationConfig->getChannel();
                if (!$this->communicationConfig->isChannelEnabled($storeId)) {
                    $message = sprintf('Integration for the channel `%s` is not enabled', $channel);
                    $result->setData(['message' => $message]);
                    return $result;
                }

                $filename = $this->feedFileService->getFeedExportFilename($this->feedType, $channel);
                $stream   = $this->streamFactory->create(['filename' => "factfinder/{$filename}"]);
                $path     = $this->feedFileService->getExportPath($filename);

                $this->feedGeneratorFactory->create($this->feedType)->generate($stream);
                $messages[] = __('<li>Feed file for channel %1 has been generated under %2</li>', $channel, "$path/$filename");

                try {
                    $this->ftpUploader->upload($filename, $stream);
                    $messages[] = __('<li>File was uploaded to the FTP server.</li>');

                    if ($this->communicationConfig->isPushImportEnabled($storeId)) {
                        try {
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
