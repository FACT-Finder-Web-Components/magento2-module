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
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig,
        private readonly LoggerInterface $logger,
    ) {}

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
        } catch (ClientExceptionInterface $exception) {
            if ($this->scopeConfig->isSetFlag('factfinder/general/logging_enabled')) {
                $this->logger->error(new Phrase(
                    'FACT-Finder response exception: %1, thrown at %2',
                    [$exception->getMessage(), $exception->getTraceAsString()]
                ));
            }
            return false;
        }
    }
}
