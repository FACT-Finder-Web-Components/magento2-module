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
        if (!$this->cmsConfig->isExportEnabled() || !$this->cmsConfig->useSeparateChannel()) {
            return;
        }

        $endpoint = $observer->getData('endpoint');
        if (strpos($endpoint, 'Suggest.ff') === false) {
            return;
        }

        try {
            $params     = ['channel' => $this->cmsConfig->getChannel()] + $observer->getData('params');
            $cmsSuggest = $this->factFinderClient->sendRequest($endpoint, $params);
            if (isset($cmsSuggest['suggestions'])) {
                array_walk($cmsSuggest['suggestions'], function (&$element) {
                    $element['type'] = 'cms'; // Change search results type to CMS
                });
                $observer->setData('response', array_merge_recursive($observer->getData('response'), $cmsSuggest));
            }
        } catch (ResponseException $e) {
            return;
        }
    }
}
