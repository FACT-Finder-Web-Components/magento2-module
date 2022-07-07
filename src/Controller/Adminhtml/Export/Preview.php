<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Omikron\Factfinder\Api\StreamInterface;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\Stream\Csv;
use Omikron\Factfinder\Model\Stream\CsvFactory;
use Omikron\Factfinder\Service\FeedFileService;

class Preview extends Action
{
    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var CsvFactory */
    private $csvFactory;

    /** @var Action\Context */
    private $context;

    /** @var RequestInterface */
    private $request;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var CommunicationConfig */
    private $communicationConfig;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonResultFactory,
        CsvFactory $csvFactory,
        FeedGeneratorFactory $feedGeneratorFactory,
        CommunicationConfig $communicationConfig
    ) {
        parent::__construct($context);
        $this->jsonResultFactory = $jsonResultFactory;
        $this->csvFactory = $csvFactory;
        $this->context = $context;
        $this->request = $this->context->getRequest();
        $this->feedGeneratorFactory = $feedGeneratorFactory;
        $this->communicationConfig = $communicationConfig;
    }

    public function execute(): Json
    {
        return $this->jsonResultFactory->create()->setData($this->getExportData());
    }

    public function getExportData(): array
    {
        $feedType = 'exportPreviewProduct';
        $entityId = (int) $this->request->getParam('entityId', 0);
        $filename = (new FeedFileService())->getFeedExportFilename($feedType, $this->communicationConfig->getChannel());
        /** @var Csv $stream */
        $stream   = $this->csvFactory->create(['filename' => "factfinder/{$filename}"]);
        $this->feedGeneratorFactory->create($feedType, ['entityId' => $entityId])->generate($stream);
        $items = $this->getItems($stream);

        return [
            'totalRecords' => count($items),
            'items' => $items,
        ];
    }

    private function getItems(StreamInterface $stream): array
    {
        $content = explode(PHP_EOL, $stream->getContent());
        $labels = explode(';', $content[0]);
        $values = array_splice($content, 1, count($content));

        return array_reduce($values, function(array $acc, string $productCsvData) use ($labels) {
            if ($productCsvData === '') {
                return $acc;
            }

            $productData = explode(';', $productCsvData);
            $acc[] = array_combine($labels, $productData);

            return $acc;
        }, []);
    }
}
