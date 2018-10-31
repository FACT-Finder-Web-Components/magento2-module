<?php

namespace Omikron\Factfinder\Controller\Export;
use Magento\Framework\App\Action\Context;


/**
 * Class Export
 * Allows to generate feed via URL
 * @package Omikron\Factfinder\Controller
 */
class Export extends \Magento\Framework\App\Action\Action
{
    const REALM = 'Restricted area';

    /** @var \Omikron\Factfinder\Helper\Data */
    protected $_dataHelper;

    /** @var \Omikron\Factfinder\Model\Export\Product */
    protected $_productModel;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $_storeManager;

    public function __construct(
        Context $context,
        \Omikron\Factfinder\Helper\Data $dataHelper,
        \Omikron\Factfinder\Model\Export\Product $productHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);
        $this->_dataHelper = $dataHelper;
        $this->_productModel = $productHelper;
        $this->_storeManager = $storeManager;
    }

    public function execute()
    {
        $validPasswords = array($this->_dataHelper->getUploadUrlUser() => $this->_dataHelper->getUploadUrlPassword());
        $validUsers = array_keys($validPasswords);

        $phpAuthUser = $_SERVER['PHP_AUTH_USER'];
        $phpAuthPw = $_SERVER['PHP_AUTH_PW'];

        $hasSuppliedCredentials = !(empty($phpAuthUser) && empty($phpAuthPw));

        $validated = ($hasSuppliedCredentials && in_array($phpAuthUser, $validUsers)) && (md5($phpAuthPw) == $validPasswords[$phpAuthUser]);

        if (!$validated) {
            header('WWW-Authenticate: Basic realm="' . self::REALM . '"');
            header('HTTP/1.0 401 Unauthorized');
            die('Not authorized.');
        }

        try {
            $this->generateCsvFile();
        } catch(\Exception $e) {
            die('Error: ' . $e->getMessage());
        }
    }

    /**
     * Generate downloadable CSV file
     */
    private function generateCsvFile()
    {
        $data = $this->_productModel->exportProductWithExternalUrl($this->_storeManager->getStore());

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $data['filename']);
        $output = fopen('php://output', 'w');

        foreach ($data['data'] as $row) {
            fputcsv($output, $row, ';');
        }
    }
}
