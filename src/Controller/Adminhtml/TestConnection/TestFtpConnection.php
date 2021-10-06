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
    /** @var string */
    private $obscuredValue = '******';

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
            $params  = $this->getConfig($this->getRealValuesFromObscured($request->getParams())) + $this->ftpConfig->toArray();
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

    private function getRealValuesFromObscured(array $params): array
    {
        if (!isset($params['ff_upload_password']) || $params['ff_upload_password'] === $this->obscuredValue) {
            $params['ff_upload_password'] = $this->ftpConfig->getUserPassword();
        }
        if (!isset($params['ff_upload_key_passphrase']) || $params['ff_upload_key_passphrase'] === $this->obscuredValue) {
            $params['ff_upload_key_passphrase'] = $this->ftpConfig->getKeyPassphrase();
        }
        return $params;
    }
}
