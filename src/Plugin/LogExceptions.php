<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Phrase;
use Omikron\Factfinder\Model\Api\PushImport;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\LoggerInterface;

class LogExceptions
{
    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(ScopeConfigInterface $scopeConfig, LoggerInterface $logger)
    {
        $this->scopeConfig = $scopeConfig;
        $this->logger      = $logger;
    }

    /**
     * @param PushImport $subject
     * @param callable   $proceed
     * @param mixed      ...$params
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundExecute($subject, callable $proceed, ...$params)
    {
        try {
            return $proceed(...$params);
        } catch (ClientExceptionInterface $e) {
            if ($this->scopeConfig->isSetFlag('factfinder/general/logging_enabled')) {
                $this->logger->error(new Phrase(
                    'FACT-Finder response exception: %1, thrown at %2',
                    [$e->getMessage(), $e->getTraceAsString()]
                ));
            }
            return false;
        }
    }
}
