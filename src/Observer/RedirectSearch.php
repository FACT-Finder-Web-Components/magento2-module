<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omikron\Factfinder\Model\Config\CommunicationConfig;

class RedirectSearch implements ObserverInterface
{
    public function __construct(
        private readonly RedirectInterface $redirect,
        private readonly ResponseInterface $response,
        private readonly CommunicationConfig $config,
    ) {
    }

    public function execute(Observer $observer)
    {
        if ($this->config->isChannelEnabled()) {
            $this->redirectRequest($observer->getData('request'));
        }
    }

    private function redirectRequest(RequestInterface $request): void
    {
        $query = $request->getParam('q', '*');
        $this->redirect->redirect($this->response, 'factfinder/result', ['_query' => ['query' => $query]]);
    }
}
