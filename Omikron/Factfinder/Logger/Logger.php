<?php

namespace Omikron\Factfinder\Logger;

use Omikron\Factfinder\Helper\Data as ConfigHelper;

/**
 * Class Logger
 */
class Logger extends \Monolog\Logger
{
    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * Logger constructor.
     *
     * @param ConfigHelper $configHelper
     * @param string       $name
     * @param array        $handlers
     * @param array        $processors
     */
    public function __construct(
        ConfigHelper $configHelper,
        $name,
        $handlers = array(),
        $processors = array())
    {
        $this->configHelper = $configHelper;

        parent::__construct($name, $handlers, $processors);
    }

    /**
     * {@inheritdoc}
     *
     * @param int    $level
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    public function addRecord($level, $message, array $context = [])
    {
        if ($this->configHelper->isLoggingEnabled()) {
            return parent::addRecord($level, $message, $context);
        }

        return false;
    }
}
