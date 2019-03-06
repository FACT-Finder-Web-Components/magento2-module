<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer\Cms;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omikron\Factfinder\Api\ClientInterface;
use Omikron\Factfinder\Exception\ResponseException;
use Omikron\Factfinder\Model\Config\CmsConfig;

class Suggest implements ObserverInterface
{
    /** @var CmsConfig */
    private $cmsConfig;

    /** @var ClientInterface */
    private $factFinderClient;

    public function __construct(
        CmsConfig $cmsConfig,
        ClientInterface $factFinderClient
    ) {
        $this->cmsConfig        = $cmsConfig;
        $this->factFinderClient = $factFinderClient;
    }

    public function execute(Observer $observer)
    {
        $endpoint = $observer->getEndpoint();
        $params   = $observer->getParams();
        if ($this->cmsConfig->isCmsExportEnabled() && $this->cmsConfig->useSeparateCmsChannel() && strpos($endpoint, 'Suggest.ff') !== false) {
            $params['channel'] = $this->cmsConfig->getChannel();
            try {
                $cmsSuggest = $this->factFinderClient->sendRequest($endpoint, $params);
                if (isset($cmsSuggest['suggestions'])) {
                    array_walk($cmsSuggest['suggestions'], function (&$element) {
                        $element['type'] = 'cms'; //Change search results type to cms
                    });
                    $observer->setResponse(array_merge_recursive($observer->getResponse(), $cmsSuggest));
                }
            } catch (ResponseException $e) {
                return;
            }
        }
    }
}
