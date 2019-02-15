<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Config\Communication;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Api\Config\ParametersSourceInterface;

class PersonalizationConfig implements ParametersSourceInterface
{
    private const  PATH_USE_FOUND_WORDS       = 'factfinder/advanced/use_found_words';
    private const  PATH_USE_ASO               = 'factfinder/advanced/use_aso';
    private const  PATH_USE_BROWSER_HISTORY   = 'factfinder/advanced/use_browser_history';
    private const  PATH_USE_PERSONALIZATION   = 'factfinder/advanced/use_personalization';
    private const  PATH_USE_SEMANTIC_ENHANCER = 'factfinder/advanced/use_semantic_enhancer';
    private const  PATH_USE_CAMPAIGNS         = 'factfinder/advanced/use_campaigns';
    private const  PATH_GENERATE_ADVISOR_TREE = 'factfinder/advanced/generate_advisor_tree';
    private const  PATH_USE_SEO               = 'factfinder/advanced/use_seo';
    private const  PATH_SEO_PREFIX            = 'factfinder/advanced/seo_prefix';
    private const  PATH_USE_ASN               = 'factfinder/advanced/use_asn';

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getParameters(): array
    {
        return [
            'use-found-words'             => [
                'value' => $this->scopeConfig->getValue(self::PATH_USE_FOUND_WORDS, ScopeInterface::SCOPE_STORES),
                'type'  => 'string',
            ],
            'use-aso'                     => [
                'value' => $this->scopeConfig->getValue(self::PATH_USE_ASO, ScopeInterface::SCOPE_STORES),
                'type'  => 'string'
            ],
            'use-browser-history'         => [
                'value' => $this->scopeConfig->getValue(self::PATH_USE_BROWSER_HISTORY, ScopeInterface::SCOPE_STORES),
                'type'  => 'string'
            ],
            'use-personalization'   => [
                'value' => $this->scopeConfig->getValue(self::PATH_USE_PERSONALIZATION, ScopeInterface::SCOPE_STORES),
                'type'  => 'string'
            ],
            'use-semantic-enhancer'       => [
                'value' => $this->scopeConfig->getValue(self::PATH_USE_SEMANTIC_ENHANCER, ScopeInterface::SCOPE_STORES),
                'type'  => 'string'
            ],
            'use-campaigns'         => [
                'value' => $this->scopeConfig->getValue(self::PATH_USE_CAMPAIGNS, ScopeInterface::SCOPE_STORES),
                'type'  => 'string'
            ],
            'generate-advisor-tree' => [
                'value' => $this->scopeConfig->getValue(self::PATH_GENERATE_ADVISOR_TREE, ScopeInterface::SCOPE_STORES),
                'type'  => 'string'
            ],
            'use-seo'               => [
                'value' => $this->scopeConfig->getValue(self::PATH_USE_SEO, ScopeInterface::SCOPE_STORES),
                'type'  => 'string'
            ],
            'seo-prefix'            => [
                'value' => $this->scopeConfig->getValue(self::PATH_SEO_PREFIX, ScopeInterface::SCOPE_STORES),
                'type'  => 'string'
            ],
            'use-asn'               => [
                'value' => $this->scopeConfig->getValue(self::PATH_USE_ASN, ScopeInterface::SCOPE_STORES),
                'type'  => 'string'
            ],
        ];
    }
}
