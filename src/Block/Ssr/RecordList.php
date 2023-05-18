<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Ssr;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http as Response;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Omikron\Factfinder\Model\FieldRoles;
use Omikron\Factfinder\Model\Ssr\SearchAdapter;

class RecordList extends Template
{
    private const RECORD_PATTERN         = '#<ff-record[\s>].*?</ff-record>#s';
    private const OPENING_RECORD_PATTERN = '#<ff-record#';

    public function __construct(
        private readonly SearchAdapter       $searchAdapter,
        private readonly SerializerInterface $jsonSerializer,
        private readonly Response            $response,
        private readonly RedirectInterface   $redirect,
        private readonly FieldRoles          $fieldRoles,
        Context                              $context,
        array                                $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _afterToHtml($html): string
    {
        // Resolve record list
        $html = preg_replace_callback('#<ff-record-list([^>]*?)>#s', function (array $match) {
            $attributes = preg_replace('#\sunresolved\s?#s', '', $match[1]);

            return "<ff-record-list ssr {$attributes}>";
        }, $html);

        $result = $this->searchResult($this->getRequest(), $this->getSearchParams());
        if ($this->shouldRedirect($result)) {
            $this->redirectToProductPage($result);

            return '';
        }

        // Add pre-rendered records
        $html = preg_replace_callback(self::RECORD_PATTERN, function (array $match) use ($result): string {
            $template = '<template data-role="record">' . $match[0] . '</template>';
            // walkaround for FFWEB-2182
            if (!count($result['records'])) {
                return $template . preg_replace(self::OPENING_RECORD_PATTERN, '<ff-record unresolved', $match[0]);
            }

            return array_reduce($result['records'] ?? [], $this->recordRenderer($match[0]), $template);
        }, $html);

        return str_replace('{FF_SEARCH_RESULT}', $this->jsonSerializer->serialize($result), $html);
    }

    protected function searchResult(RequestInterface $request, array $searchParams): array
    {
        //workaround for FFWEB-2720
        if (!empty($request->getCookie('ffwebc_sid', null))) {
            $sid = sprintf('sid=%s', $request->getCookie('ffwebc_sid', ''));
        } elseif (!empty($_COOKIE['ffwebc_sid'])) { //@phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageWarning
            $sid = sprintf('sid=%s', $_COOKIE['ffwebc_sid']); //@phpcs:ignore Magento2.Security.Superglobal.SuperglobalUsageWarning
        } else {
            $sid = 'sid=';
        }

        $paramsString = implode('&', array_filter([
                parse_url($request->getRequestString(), PHP_URL_QUERY), //@phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
                http_build_query($searchParams),
                $sid
        ]));

        return $this->searchAdapter->search($paramsString, $this->getRequest()->getFullActionName() === 'catalog_category_view');
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
        $deepLink       = $result['records'][0]['record'][$this->fieldRoles->getFieldRole('deeplink')];
        $productPageUrl = $this->isAbsoluteUrl($deepLink) ? $deepLink : $this->removeForwardSlash($deepLink);

        $this->redirect->redirect($this->response, $productPageUrl);
    }

    private function shouldRedirect(array $result): bool
    {
        $isExactSearch = isset($result['articleNumberSearch']) && $result['articleNumberSearch']
            || isset($result['resultArticleNumberStatus']) && $result['resultArticleNumberStatus'] === 'resultsFound';

        return count($result['records']) === 1 && $isExactSearch;
    }

    private function isAbsoluteUrl(string $url): bool
    {
        return (bool) preg_match('#http(s)?:\/\/#iu', $url);
    }

    private function removeForwardSlash(string $url): string
    {
        return preg_replace('/\//', '', $url);
    }
}
