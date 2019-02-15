<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\FF;

use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Xml\Parser;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Api\SessionDataInterface;
use Omikron\Factfinder\Helper\Data;

class Communication extends Template
{
    /** @var Reader */
    private $moduleDirReader;

    /** @var Data */
    private $configHelper;

    /** @var Parser */
    private $parser;

    /** @var CommunicationConfigInterface */
    private $communicationConfig;

    /** @var SessionDataInterface */
    private $sessionData;

    /** @var FieldRolesInterface */
    private $fieldRoles;

    /** @var SerializerInterface */
    private $serializer;

    /** @var array */
    private $configData = [];

    /** @var array */
    private $requiredAttributes = ['url', 'channel', 'sid', 'version'];

    public function __construct(
        Context $context,
        Data $helper,
        Reader $moduleDirReader,
        Parser $parser,
        SessionDataInterface $sessionData,
        FieldRolesInterface $fieldRoles,
        CommunicationConfigInterface $communicationConfig,
        SerializerInterface $serializer,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->configHelper        = $helper;
        $this->communicationConfig = $communicationConfig;
        $this->moduleDirReader     = $moduleDirReader;
        $this->parser              = $parser;
        $this->sessionData         = $sessionData;
        $this->fieldRoles          = $fieldRoles;
        $this->serializer          = $serializer;
    }

    protected function _beforeToHtml()
    {
        $filePath      = $this->moduleDirReader->getModuleDir('etc', 'Omikron_Factfinder') . '/config.xml';
        $defaultValues = $this->parser->load($filePath)->xmlToArray()['config']['_value']['default']['factfinder'];

        $this->configData = [
            'url' => [
                'value' => $this->getServerUrl(),
                'type' => 'string',
                'defaultValue' => null
            ],
            'sid' => [
                'value' => $this->sessionData->getSessionId(),
                'type' => 'string',
                'defaultValue' => null
            ],
            'user-id' => [
                'value' => $this->sessionData->getUserId() ?: null,
                'type' => 'string',
                'defaultValue' => null
            ],
            'version' => [
                'value' => $this->configHelper->getVersion(),
                'type' => 'string',
                'defaultValue' => $defaultValues['advanced']['version']
            ],
            'default-query' => [
                'value' => $this->configHelper->getDefaultQuery(),
                'type' => 'string',
                'defaultValue' => $defaultValues['advanced']['default_query']
            ],
            'channel' => [
                'value' => $this->communicationConfig->getChannel(),
                'type' => 'string',
                'defaultValue' => $defaultValues['general']['channel']
            ],
            'use-url-parameter' => [
                'value' => $this->configHelper->getUseUrlParameter(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_url_parameter']
            ],
            'use-cache' => [
                'value' => $this->configHelper->getUseCache(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_cache']
            ],
            'add-params' => [
                'value' => $this->configHelper->getAddParams(),
                'type' => 'string',
                'defaultValue' => $defaultValues['advanced']['add_params']
            ],
            'add-tracking-params' => [
                'value' => $this->configHelper->getAddTrackingParams(),
                'type' => 'string',
                'defaultValue' => $defaultValues['advanced']['add_tracking_params']
            ],
            'keep-url-params' => [
                'value' => $this->configHelper->getKeepUrlParams(),
                'type' => 'string',
                'defaultValue' => $defaultValues['advanced']['keep_url_params']
            ],
            'use-asn' => [
                'value' => $this->configHelper->getUseAsn(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_asn']
            ],
            'use-found-words' => [
                'value' => $this->configHelper->getUseFoundWords(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_found_words']
            ],
            'use-campaigns' => [
                'value' => $this->configHelper->getUseCampaigns(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_campaigns']
            ],
            'generate-advisor-tree' => [
                'value' => $this->configHelper->getGenerateAdvisorTree(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['generate_advisor_tree']
            ],
            'disable-cache' => [
                'value' => $this->configHelper->getDisableCache(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['disable_cache']
            ],
            'use-personalization' => [
                'value' => $this->configHelper->getUsePersonalization(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_personalization']
            ],
            'use-semantic-enhancer' => [
                'value' => $this->configHelper->getUseSemanticEnhancer(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_semantic_enhancer']
            ],
            'use-aso' => [
                'value' => $this->configHelper->getUseAso(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_aso']
            ],
            'use-browser-history' => [
                'value' => $this->configHelper->getUseBrowserHistory(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_browser_history']
            ],
            'use-seo' => [
                'value' => $this->configHelper->getUseSeo(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_seo']
            ],
            'seo-prefix' => [
                'value' => $this->configHelper->getSeoPrefix(),
                'type' => 'string',
                'defaultValue' => $defaultValues['advanced']['seo_prefix']
            ],
            'search-immediate' => [
                'value' => $this->getData('search_immediate') ?: $this->configHelper->getSearchImmediate(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['search_immediate']
            ],
            'disable-single-hit-redirect'=> [
                'value' => $this->configHelper->getDisableSingleHitRedirect(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['disable_single_hit_redirect']
            ],
        ];
    }

    public function getWebComponent(): string
    {
        return self::buildXMLElement('ff-communication', self::generateAttributes($this->configData, $this->requiredAttributes));
    }

    public function getFieldRoles(): string
    {
        return (string) $this->serializer->serialize($this->fieldRoles->getFieldRoles());
    }

    /**
     * XML element builder
     *
     * @param string $rootElement - name of rootElement
     * @param array $attributes - as key/value pairs
     * @return string - xml element as a string
     */
    private static function buildXMLElement($rootElement, $attributes)
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->setIndent(true);
        $writer->startElement($rootElement);
        foreach ($attributes as $key => $value) {
            $writer->startAttribute($key);
            $writer->text((string) $value);
            $writer->endAttribute();
        }
        $writer->fullEndElement();
        return $writer->outputMemory();
    }

    private static function generateAttributes($configData, $requiredAttributes)
    {
        $result = [];
        foreach ($configData as $name => $info) {
            if ($info['value'] !== $info['defaultValue'] || in_array($name, $requiredAttributes)) {
                if ($info['type'] === 'boolean') {
                    $result[$name] = (int) $configData[$name]['value'] ? 'true' : 'false';
                } else {
                    $result[$name] = $configData[$name]['value'];
                }
            }
        }
        return $result;
    }

    private function getServerUrl(): string
    {
        if ($this->configHelper->isEnrichmentEnabled()) {
            return $this->getUrl('', ['_direct' => Data::FRONT_NAME]);
        }
        return $this->communicationConfig->getAddress();
    }
}
