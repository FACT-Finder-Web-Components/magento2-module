<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Controller\Adminhtml\TestConnection;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Phrase;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Api\CredentialsFactory;
use Omikron\Factfinder\Model\Config\FtpConfig;
use Omikron\Factfinder\Model\FtpUploader;

class TestFtpConnection extends Action
{
    /** @var JsonFactory */
    private $jsonResultFactory;

    /** @var  FtpUploader */
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
            $params  = $this->getConfig($request->getParams());


            $serverUrl = $request->getParam('address', $this->communicationConfig->getAddress());
            $this->testConnection->execute($serverUrl, $params);
        } catch (ResponseException $e) {
            $message = $e->getMessage();
        }

        return $this->jsonResultFactory->create()->setData(['message' => $message]);
    }

    private function getConfig(array $params): array
    {
        array_walk($params, function (string $_v, string $key) {
            preg_replace('ff_upload_', '', $key);
        });

        return $params + $this->ftpConfig->toArray();
    }
}
