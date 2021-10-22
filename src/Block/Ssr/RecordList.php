<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Ssr;

use Magento\Framework\App\Response\Http as Response;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Omikron\Factfinder\Model\FieldRoles;
use Omikron\Factfinder\Model\Ssr\SearchAdapter;

class RecordList extends Template
{
    private const RECORD_PATTERN = '#<ff-record[\s>].*?</ff-record>#s';

    /** @var SearchAdapter */
    protected $searchAdapter;

    /** @var SerializerInterface */
    protected $jsonSerializer;

    /** @var Response */
    private $response;

    /** @var RedirectInterface */
    private $redirect;

    /** @var FieldRoles */
    private $fieldRoles;

    public function __construct(
        Template\Context    $context,
        SearchAdapter       $searchAdapter,
        SerializerInterface $jsonSerializer,
        Response            $response,
        RedirectInterface   $redirect,
        FieldRoles          $fieldRoles,
        array               $data = []
    ) {
        parent::__construct($context, $data);
        $this->searchAdapter = $searchAdapter;
        $this->jsonSerializer = $jsonSerializer;
        $this->redirect       = $redirect;
        $this->response       = $response;
        $this->fieldRoles     = $fieldRoles;
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

        $result = $this->searchResult($this->getRequest()->getParam('query', '*'), $this->getSearchParams());
        if ($this->shouldRedirect($result)) {
            $this->redirectToProductPage($result);
        }

        // Add pre-rendered records
        $html = preg_replace_callback(self::RECORD_PATTERN, function (array $match) use ($result): string {
            // $match[0] is added twice due to SSR bug in Web Components. ff-record template need to be added one time
            // as a template and one time as regular ff-record element
            $template = '<template data-role="record">' . $match[0] .'</template>' . $match[0];
            return array_reduce($result['records'] ?? [], $this->recordRenderer($match[0]), $template);
        }, $html);

        return str_replace('{FF_SEARCH_RESULT}', $this->jsonSerializer->serialize($result), $html);
    }

    protected function searchResult(string $query, array $params): array
    {
        return $this->searchAdapter->search($query, $params);
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

    protected function redirectToProductPage(array $result): void
    {
        $this->redirect->redirect($this->response, $result['records'][0]['record'][$this->fieldRoles->getFieldRole('deeplink')]);
    }

    private function shouldRedirect(array $result): bool
    {
        $isExactSearch = isset($result['articleNumberSearch']) && $result['articleNumberSearch']
            || isset($result['resultArticleNumberStatus']) && $result['resultArticleNumberStatus'] === 'resultsFound';
        return count($result['records']) === 1 && $isExactSearch;
    }
}
