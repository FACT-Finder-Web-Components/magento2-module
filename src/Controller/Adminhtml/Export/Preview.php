<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Omikron\Factfinder\Api\StreamInterface;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\Stream\Json as JsonStream;
use Omikron\Factfinder\Utilities\Validator\ExportPreviewValidator;

class Preview extends Action
{
    private JsonFactory $jsonResultFactory;
    private Action\Context $context;
    private RequestInterface $request;
    private FeedGeneratorFactory $feedGeneratorFactory;
    private ProductRepositoryInterface $productRepository;
    private ConfigurableType $configurableType;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonResultFactory,
        FeedGeneratorFactory $feedGeneratorFactory,
        ProductRepositoryInterface $productRepository,
        ConfigurableType $configurableType,
    ) {
        parent::__construct($context);
        $this->jsonResultFactory = $jsonResultFactory;
        $this->context = $context;
        $this->request = $this->context->getRequest();
        $this->feedGeneratorFactory = $feedGeneratorFactory;
        $this->productRepository = $productRepository;
        $this->configurableType = $configurableType;
    }

    public function execute(): Json
    {
        $response = $this->jsonResultFactory->create();

        try {
            $entityId = (int) $this->request->getParam('entityId', 0);
            (new ExportPreviewValidator($this->productRepository, $this->configurableType, $entityId))->validate();

            return $response->setData($this->getExportData($entityId));
        } catch (\Throwable $e) {
            return $response->setData(['message' => $e->getMessage()]);
        }
    }

    public function getExportData(int $entityId): array
    {
        $feedType = 'exportPreviewProduct';
        $stream = new JsonStream();
        $this->feedGeneratorFactory->create($feedType, ['entityId' => $entityId])->generate($stream);
        $items = $this->getItems($stream);

        return [
            'totalRecords' => count($items),
            'items' => $items,
        ];
    }

    private function getItems(StreamInterface $stream): array
    {
        $content = json_decode($stream->getContent(), true);

        return array_splice($content, 1, count($content));
    }
}
