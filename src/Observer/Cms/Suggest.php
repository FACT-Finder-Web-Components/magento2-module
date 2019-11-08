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
            $cmsSuggest = $this->factFinderClient->sendRequest($endpoint, $params)['suggestions'] ?? [];

            $response = ((array) $observer->getData('response')) + ['suggestions' => []];
            foreach ($cmsSuggest as $item) {
                array_push($response['suggestions'], ['type' => 'cms'] + $item);
            }
            $observer->setData('response', $response);
        } catch (ResponseException $e) {
            return;
        }
    }
}
