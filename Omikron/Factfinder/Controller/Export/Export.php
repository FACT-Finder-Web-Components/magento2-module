<?php

namespace Omikron\Factfinder\Controller\Export;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Omikron\Factfinder\Model\Export\Product as ProductExport;

/**
 * Class Export
 * Allows to generate feed via URL
 *
 * @package Omikron\Factfinder\Controller
 */
class Export extends Action
{
    /** @var ProductExport */
    protected $productExport;

    /** @var StoreManagerInterface */
    protected $storeManager;

    public function __construct(
        Context $context,
        ProductExport $productExport,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->productExport = $productExport;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        $data = $this->productExport->exportProductWithExternalUrl($this->storeManager->getStore());

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $data['filename']);
        $output = fopen('php://output', 'w');

        foreach ($data['data'] as $row) {
            fputcsv($output, $row, ';');
        }
    }
}
