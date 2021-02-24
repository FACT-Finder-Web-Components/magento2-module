<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Ssr;

use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;
use Omikron\Factfinder\Model\Ssr\SearchAdapter;

class RecordList extends Template
{
    private const RECORD_PATTERN = '#<ff-record[\s>].*?</ff-record>#s';

    /** @var SearchAdapter */
    protected $searchAdapter;

    /** @var CommunicationConfigInterface  */
    protected $communicationConfig;

    /** @var SerializerInterface */
    protected $jsonSerializer;

    public function __construct(
        Template\Context $context,
        SearchAdapter $searchAdapter,
        CommunicationConfigInterface $communicationConfig,
        SerializerInterface $jsonSerializer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->searchAdapter       = $searchAdapter;
        $this->communicationConfig = $communicationConfig;
        $this->jsonSerializer      = $jsonSerializer;
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _afterToHtml($html)
    {
        // Resolve record list
        $html = preg_replace_callback('#<ff-record-list([^>]*?)>#s', function (array $match) {
            $attributes = preg_replace('#\sunresolved\s?#s', '', $match[1]);
            return "<ff-record-list ssr {$attributes}>";
        }, $html);

        $channel = $this->communicationConfig->getChannel();
        $result  = $this->searchResult($channel, $this->getRequest()->getParam('query', '*'), $this->getSearchParams());

        // Add pre-rendered records
        $html = preg_replace_callback(self::RECORD_PATTERN, function (array $match) use ($result): string {
            $template = '<template data-role="record">' . $match[0] . '</template>';
            return array_reduce($result['records'] ?? [], $this->recordRenderer($match[0]), $template);
        }, $html);

        return str_replace('{FF_SEARCH_RESULT}', $this->jsonSerializer->serialize($result), $html);
    }

    protected function searchResult(string $channel, string $query, array $params): array
    {
        return $this->searchAdapter->search($channel, $query, $params);
    }

    protected function recordRenderer(string $template): callable
    {
        return function (string $initial, array $record) use ($template): string {
            $this->assign($record);
            $templateEngine = $this->templateEnginePool->get('mustache');
            return $initial . $templateEngine->render($this->templateContext, $template, $this->_viewVars);
        };
    }

    protected function getSearchParams(): array
    {
        $params = explode(',', (string) $this->getData('search_params'));
        return array_reduce(array_filter($params), function (array $result, string $part): array {
            [$key, $value] = array_map('urldecode', explode('=', $part));
            return $result + [$key => $value];
        }, []);
    }
}
