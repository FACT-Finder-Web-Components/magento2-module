<?php

namespace Omikron\Factfinder\Observer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\HTTP\Authentication as Credentials;
use Omikron\Factfinder\Model\Export\BasicAuth as Authentication;

class ExportAuthentication implements ObserverInterface
{
    /** @var ActionFlag */
    private $actionFlag;

    /** @var Authentication */
    private $authentication;

    /** @var Credentials */
    private $credentials;

    public function __construct(
        ActionFlag $actionFlag,
        Authentication $authentication,
        Credentials $credentials
    ) {
        $this->actionFlag = $actionFlag;
        $this->authentication = $authentication;
        $this->credentials = $credentials;
    }

    public function execute(Observer $observer)
    {
        if (!$this->authentication->authenticate(...$this->credentials->getCredentials())) {
            $this->credentials->setAuthenticationFailed('FACT-Finder');
            $this->actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
        }
    }
}
