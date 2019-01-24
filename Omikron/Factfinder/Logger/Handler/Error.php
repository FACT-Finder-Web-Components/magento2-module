<?php

namespace Omikron\Factfinder\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Error
 */
class Error extends Base
{
  const FILENAME = '/var/log/ff-error.log';

    /**
     * @var string
     */
    protected $fileName = self::FILENAME;

    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::ERROR;
}
