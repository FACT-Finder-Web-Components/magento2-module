<?php

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Omikron\Factfinder\Api\ExporterInterface;
use Omikron\Factfinder\Model\Export\DataProvidersFactory;
use Omikron\Factfinder\Model\Export\Stream\CsvFactory;

class Feed extends Action
{
    /** @var JsonFactory */
    private $resultJsonFactory;

    /** @var ExporterInterface */
    private $productExport;

    /** @var DataProvidersFactory */
    private $dataProvidersFactory;

    /** @var CsvFactory */
    private $streamWriterFactory;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        DataProvidersFactory $dataProvidersFactory,
        CsvFactory $streamFactory,
        ExporterInterface $export
    ) {
        parent::__construct($context);
        $this->resultJsonFactory    = $resultJsonFactory;
        $this->productExport        = $export;
        $this->dataProvidersFactory = $dataProvidersFactory;
        $this->streamWriterFactory  = $streamFactory;
    }

    public function execute(): Json
    {
        $streamWriter = $this->streamWriterFactory->create(['fileName' => 'product_export.csv']);
        $this->productExport->exportEntities($streamWriter, $this->dataProvidersFactory->create());
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData(['message' => __('Feed was sucessfully exported')]);
    }
}
