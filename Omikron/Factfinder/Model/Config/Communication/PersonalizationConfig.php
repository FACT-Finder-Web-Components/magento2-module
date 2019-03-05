<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Communication;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class PersonalizationConfig implements ParametersSourceInterface
{
    private const PATH_USE_FOUND_WORDS       = 'factfinder/advanced/use_found_words';
    private const PATH_USE_ASO               = 'factfinder/advanced/use_aso';
    private const PATH_USE_BROWSER_HISTORY   = 'factfinder/advanced/use_browser_history';
    private const PATH_USE_PERSONALIZATION   = 'factfinder/advanced/use_personalization';
    private const PATH_USE_SEMANTIC_ENHANCER = 'factfinder/advanced/use_semantic_enhancer';
    private const PATH_USE_CAMPAIGNS         = 'factfinder/advanced/use_campaigns';
    private const PATH_GENERATE_ADVISOR_TREE = 'factfinder/advanced/generate_advisor_tree';
    private const PATH_USE_ASN               = 'factfinder/advanced/use_asn';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getParameters(): array
    {
        return [
            'use-found-words'       => $this->getConfig(self::PATH_USE_FOUND_WORDS),
            'use-aso'               => $this->getConfig(self::PATH_USE_ASO),
            'use-browser-history'   => $this->getConfig(self::PATH_USE_BROWSER_HISTORY),
            'use-personalization'   => $this->getConfig(self::PATH_USE_PERSONALIZATION),
            'use-semantic-enhancer' => $this->getConfig(self::PATH_USE_SEMANTIC_ENHANCER),
            'use-campaigns'         => $this->getConfig(self::PATH_USE_CAMPAIGNS),
            'generate-advisor-tree' => $this->getConfig(self::PATH_GENERATE_ADVISOR_TREE),
            'use-asn'               => $this->getConfig(self::PATH_USE_ASN),
        ];
    }

    private function getConfig(string $path): string
    {
        return (string) $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORES);
    }
}
