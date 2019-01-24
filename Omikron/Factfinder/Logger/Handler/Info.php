<?php

namespace Omikron\Factfinder\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;

/**
 * Class Info
 */
class Info extends Base
{
    const FILENAME = '/var/log/ff-info.log';

    /**
     * @var string
     */
    protected $fileName = self::FILENAME;

    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::INFO;
}
