<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Phrase;
use Omikron\Factfinder\Model\Config\FtpConfig;
use Omikron\Factfinder\Model\FtpUploader;

class TestFtpConnection extends Action
{
    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var FtpUploader */
    private $ftpUploader;

    /** @var FtpConfig */
    private $ftpConfig;

    public function __construct(
        Action\Context $context,
        JsonFactory $jsonResultFactory,
        FtpUploader $ftpUploader,
        FtpConfig $ftpConfig
    ) {
        parent::__construct($context);
        $this->jsonResultFactory = $jsonResultFactory;
        $this->ftpUploader       = $ftpUploader;
        $this->ftpConfig         = $ftpConfig;
    }

    public function execute()
    {
        $message = new Phrase('Connection successfully established.');

        try {
            $request = $this->getRequest();
            $params  = $this->getConfig($request->getParams()) + $this->ftpConfig->toArray();
            $this->ftpUploader->testConnection($params);
        } catch (\Exception $e) {
            $message = $e->getMessage();
        }

        return $this->jsonResultFactory->create()->setData(['message' => $message]);
    }

    private function getConfig(array $params): array
    {
        $prefix   = 'ff_upload_';
        $filtered = array_filter($params, function (string $key) use ($prefix): bool {
            return (bool) preg_match("#^{$prefix}#", $key);
        }, ARRAY_FILTER_USE_KEY);

        return array_combine(array_map(function (string $key) use ($prefix): string {
            return str_replace($prefix, '', $key);
        }, array_keys($filtered)), array_values($filtered));
    }
}
