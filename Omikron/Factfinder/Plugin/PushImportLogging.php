<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Omikron\Factfinder\Exception\RequestException;
use Omikron\Factfinder\Model\Consumer\PushImport;
use Psr\Log\LoggerInterface;

class PushImportLogging
{
    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    /** @var LoggerInterface  */
    protected $logger;

    public function __construct(ScopeConfigInterface $scopeConfig, LoggerInterface $logger)
    {
        $this->scopeConfig = $scopeConfig;
        $this->logger      = $logger;
    }

    public function aroundExecute(PushImport $subject, callable $proceed, array $params = [], string $scopeId = null)
    {
        try {
            $result = $proceed($params, $scopeId);

            return $result;
        } catch (RequestException $e) {
            if ($this->scopeConfig->isSetFlag('factfinder/general/logging_enabled')) {
                $this->logger->error(__(
                        'Exception %1  thrown at %2. FACT-Finder response : %3',
                        $e->getMessage(), $e->getTraceAsString(), $e->getResponseBody()
                    )
                );
            }
        }
    }
}
