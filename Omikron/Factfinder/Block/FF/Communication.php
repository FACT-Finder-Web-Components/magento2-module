<?php

namespace Omikron\Factfinder\Block\FF;

use Magento\Framework\Module\Dir\Reader;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Xml\Parser;
use Omikron\Factfinder\Helper\Data;
use Omikron\Factfinder\Helper\Tracking;

/**
 * Block Class FF Communication
 * @package Omikron\Factfinder\Block\FF
 */
class Communication extends Template
{
    /** @var \Magento\Framework\Module\Dir\Reader */
    protected $_moduleDirReader;
    /** @var Data */
    private $_helper;
    /** @var array */
    private $_configData;
    /** @var array */
    private $_requiredAttributes;
    /** @var \Magento\Framework\Xml\Parser */
    private $_parser;

    /**
     * Communication constructor.
     *
     * @param Template\Context $context
     * @param Data $helper
     * @param Reader $moduleDirReader
     * @param Parser $parser
     * @param Tracking $tracking
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $helper,
        Reader $moduleDirReader,
        Parser $parser,
        Tracking $tracking, $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;

        $this->_moduleDirReader = $moduleDirReader;
        $this->_parser = $parser;

        $filePath = $this->_moduleDirReader->getModuleDir('etc', 'Omikron_Factfinder') . '/config.xml';
        $defaultValues = $this->_parser->load($filePath)->xmlToArray()['config']['_value']['default']['factfinder'];

        $this->_requiredAttributes = ['url', 'channel', 'sid', 'version'];

        $this->_configData = [
            'url' => [
                'value' =>  ($this->_helper->isEnrichmentEnabled() ? '/' . Data::FRONT_NAME . '/' : $this->_helper->getAddress()),
                'type' => 'string',
                'defaultValue' => null
            ],
            'sid' => [
                'value' => $this->_helper->getCorrectSessionId($tracking->getSessionId()),
                'type' => 'string',
                'defaultValue' => null
            ],
            'user-id' => [
                'value' => $tracking->getUserId(),
                'type' => 'string',
                'defaultValue' => null
            ],
            'version' => [
                'value' => $this->_helper->getVersion(),
                'type' => 'string',
                'defaultValue' => $defaultValues['advanced']['version']
            ],
            'default-query' => [
                'value' => $this->_helper->getDefaultQuery(),
                'type' => 'string',
                'defaultValue' => $defaultValues['advanced']['default_query']
            ],
            'channel' => [
                'value' => $this->_helper->getChannel(),
                'type' => 'string',
                'defaultValue' => $defaultValues['general']['channel']
            ],
            'use-url-parameter' => [
                'value' => $this->_helper->getUseUrlParameter(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_url_parameter']
            ],
            'use-cache' => [
                'value' => $this->_helper->getUseCache(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_cache']
            ],
            'add-params' => [
                'value' => $this->_helper->getAddParams(),
                'type' => 'string',
                'defaultValue' => $defaultValues['advanced']['add_params']
            ],
            'add-tracking-params' => [
                'value' => $this->_helper->getAddTrackingParams(),
                'type' => 'string',
                'defaultValue' => $defaultValues['advanced']['add_tracking_params']
            ],
            'keep-url-params' => [
                'value' => $this->_helper->getKeepUrlParams(),
                'type' => 'string',
                'defaultValue' => $defaultValues['advanced']['keep_url_params']
            ],
            'use-asn' => [
                'value' => $this->_helper->getUseAsn(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_asn']
            ],
            'use-found-words' => [
                'value' => $this->_helper->getUseFoundWords(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_found_words']
            ],
            'use-campaigns' => [
                'value' => $this->_helper->getUseCampaigns(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_campaigns']
            ],
            'generate-advisor-tree' => [
                'value' => $this->_helper->getGenerateAdvisorTree(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['generate_advisor_tree']
            ],
            'disable-cache' => [
                'value' => $this->_helper->getDisableCache(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['disable_cache']
            ],
            'use-personalization' => [
                'value' => $this->_helper->getUsePersonalization(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_personalization']
            ],
            'use-semantic-enhancer' => [
                'value' => $this->_helper->getUseSemanticEnhancer(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_semantic_enhancer']
            ],
            'use-aso' => [
                'value' => $this->_helper->getUseAso(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_aso']
            ],
            'use-browser-history' => [
                'value' => $this->_helper->getUseBrowserHistory(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_browser_history']
            ],
            'use-seo' => [
                'value' => $this->_helper->getUseSeo(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['use_seo']
            ],
            'seo-prefix' => [
                'value' => $this->_helper->getSeoPrefix(),
                'type' => 'string',
                'defaultValue' => $defaultValues['advanced']['seo_prefix']
            ],
            'search-immediate' => [
                'value' => $this->_helper->getSearchImmediate(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['search_immediate']
            ],
            'disable-single-hit-redirect'=> [
                'value' => $this->_helper->getDisableSingleHitRedirect(),
                'type' => 'boolean',
                'defaultValue' => $defaultValues['advanced']['disable_single_hit_redirect']
            ],
        ];

        // always enable "search-immediate" when on result page
        if ($this->getRequest()->getControllerName() == Data::CUSTOM_RESULT_PAGE) {
            $this->_configData['search-immediate']['value'] = 1;
            $this->_configData['search-immediate']['defaultValue'] = 0;
        }
    }

    /**
     * Return the FF WebComponent
     *
     * @return string
     */
    public function getWebComponent()
    {
        return self::buildXMLElement('ff-communication', self::generateAttributes($this->_configData, $this->_requiredAttributes));
    }

    /**
     * Returns all fields used as tracking id
     * @return string
     */
    public function getFieldRoles()
    {
        return $this->_helper->getFieldRoles();
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
            $writer->text($value);
            $writer->endAttribute();
        }
        $writer->fullEndElement();
        return $writer->outputMemory();
    }

    /**
     * Export the configData to attributes for the FF-WebComponent
     *
     * @param array $configData - $configData array
     * @param array $requiredAttributes - attributes that are put into component even if they are empty or use default value
     * @return array
     */
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
}
