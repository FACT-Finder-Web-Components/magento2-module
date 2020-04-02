<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Ssr;

use Magento\Framework\View\Element\Template;
use Omikron\Factfinder\Model\Ssr\SearchAdapter;

class RecordList extends Template
{
    /** @var SearchAdapter */
    private $searchAdapter;

    public function __construct(Template\Context $context, SearchAdapter $searchAdapter, array $data = [])
    {
        parent::__construct($context, $data);
        $this->searchAdapter = $searchAdapter;
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _toHtml()
    {
        $output = $this->getChildHtml();
        $output = preg_replace_callback('#<ff-record-list([^>]*?)>#s', function (array $match) {
            $attributes = preg_replace('#\sunresolved\s?#s', '', $match[1]);
            return "<ff-record-list ssr {$attributes}>";
        }, $output);

        return preg_replace_callback('#<ff-record[\s>].*?</ff-record>#s', function (array $match): string {
            return $this->preRendered($match[0]) . '<template data-role="record">' . $match[0] . '</template>';
        }, $output);
    }

    public function preRendered(string $template): string
    {
        $records = $this->searchAdapter->search($this->getRequest()->getParam('query', '*'), $this->getSearchParams());
        return implode('', array_map($this->renderRecord($template), $records));
    }

    private function renderRecord(string $template): callable
    {
        return function (array $record) use ($template): string {
            $this->assign($record);
            $templateEngine = $this->templateEnginePool->get('mustache');
            return $templateEngine->render($this->templateContext, $template, $this->_viewVars);
        };
    }

    private function getSearchParams(): array
    {
        $params = explode(',', (string) $this->getData('search_params'));
        return array_reduce(array_filter($params), function (array $result, string $part): array {
            [$key, $value] = array_map('urldecode', explode('=', $part));
            return $result + [$key => $value];
        }, []);
    }
}
