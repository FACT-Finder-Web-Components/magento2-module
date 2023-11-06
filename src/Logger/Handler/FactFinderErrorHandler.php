<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Logger\Handler;

use Magento\Framework\Logger\Handler\Base as BaseHandler;
use Monolog\Logger as MonologLogger;

class FactFinderErrorHandler extends BaseHandler
{
    protected $loggerType = MonologLogger::ERROR;
    protected $fileName = '/var/log/factfinder.log';
}
