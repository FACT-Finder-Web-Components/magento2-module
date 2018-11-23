<?php
namespace Omikron\Factfinder\Model\Adminhtml\System\Config\Backend\Feed;

/**
 * Class Frequency
 */
class Frequency extends \Magento\Framework\App\Config\Value
{
    const PATH_CRON_TIME       = 'ff_cron_time';
    const PATH_CRON_IS_ENABLED = 'factfinder/configurable_cron/ff_cron_enabled';
    const PATH_CRON_FREQUENCY  = 'factfinder/configurable_cron/ff_cron_frequency';
    const CRON_STRING_PATH     = 'crontab/default/jobs/factfinder_feed_export/schedule/cron_expr';

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * Frequency constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $config
     * @param \Magento\Framework\App\Cache\TypeListInterface               $cacheTypeList
     * @param \Magento\Framework\App\Config\Storage\WriterInterface        $configWriter
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->configWriter = $configWriter;
    }

    /**
     * @return \Magento\Framework\App\Config\Value|void
     */
    public function afterSave()
    {
        $time      = $this->getFieldsetDataValue(self::PATH_CRON_TIME);
        $frequency = $this->getValue();

        $frequencyWeekly  = \Magento\Cron\Model\Config\Source\Frequency::CRON_WEEKLY;
        $frequencyMonthly = \Magento\Cron\Model\Config\Source\Frequency::CRON_MONTHLY;

        $cronExprArray  = array(
            intval($time[1]),
            intval($time[0]),
            ($frequency == $frequencyMonthly) ? '1' : '*',
            '*',
            ($frequency == $frequencyWeekly) ? '1' : '*',
        );
        $cronExprString = join(' ', $cronExprArray);

        try {
            $this->configWriter->save(self::CRON_STRING_PATH, $cronExprString);
        } catch (Exception $e) {
            throw new Exception('Unable to save the cron expression.');
        }

        return $this;
    }
}
