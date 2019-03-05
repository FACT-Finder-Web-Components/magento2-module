<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\Export;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\Export\Stream\FtpFactory;
use Omikron\Factfinder\Api\ExporterInterface;
use Omikron\Factfinder\Model\Export\Cms\DataProvider;
use Omikron\Factfinder\Model\Exporter;
use Magento\Framework\Controller\Result\Json;

class CmsFeed extends Action
{
    /** @var PageFactory */
    private $resultPageFactory;

    /** @var JsonFactory */
    private $resultJsonFactory;

    /** @var Exporter */
    private $cmsExporter;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var DataProvider */
    private $dataProvider;

    /** @var FtpFactory */
    private $streamWriterFactory;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory,
        StoreManagerInterface $storeManager,
        DataProvider $dataProvider,
        FtpFactory $streamFactory,
        ExporterInterface $cmsExporter
    ) {
        $this->resultPageFactory   = $resultPageFactory;
        $this->resultJsonFactory   = $resultJsonFactory;
        $this->storeManager        = $storeManager;
        $this->cmsExporter         = $cmsExporter;
        $this->dataProvider        = $dataProvider;
        $this->streamWriterFactory = $streamFactory;

        parent::__construct($context);
    }

    public function execute(): Json
    {
        preg_match('@/store/([0-9]+)/@', (string) $this->_redirect->getRefererUrl(), $result);
        $streamWriter = $this->streamWriterFactory->create(['fileName' => 'cms_export.csv']);
        $this->cmsExporter->exportEntities($streamWriter, $this->dataProvider);
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData(['message' => __('Feed was sucessfully exported')]);
    }
}
