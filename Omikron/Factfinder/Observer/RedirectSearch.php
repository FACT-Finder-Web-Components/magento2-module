<?php

namespace Omikron\Factfinder\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Omikron\Factfinder\Api\Config\CommunicationConfigInterface;

class RedirectSearch implements ObserverInterface
{
    /** @var RedirectInterface */
    private $redirect;

    /** @var ResponseInterface */
    private $response;

    /** @var Config */
    private $config;

    public function __construct(
        RedirectInterface $redirect,
        ResponseInterface $response,
        CommunicationConfigInterface $config
    ) {
        $this->redirect = $redirect;
        $this->response = $response;
        $this->config = $config;
    }

    public function execute(Observer $observer)
    {
        if ($this->config->isEnabled()) {
            $this->redirectRequest($observer->getData('request'));
        }
    }

    private function redirectRequest(RequestInterface $request): void
    {
        $query = $request->getParam('q', $this->config->getDefaultQuery());
        $this->redirect->redirect($this->response, 'FACT-Finder/result', ['_query' => ['query' => $query]]);
    }
}
