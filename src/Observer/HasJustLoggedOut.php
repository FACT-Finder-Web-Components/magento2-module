<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Observer;

use Magento\Framework\Event\Observer;

class HasJustLoggedOut extends LoginStateObserver
{
    /**
     * @param Observer $_
     *
     * @SuppressWarnings(PHPMD)
     */
    public function execute(Observer $_): void
    {
        $this->setCookie(self::HAS_JUST_LOGGED_OUT, '1');
    }
}
