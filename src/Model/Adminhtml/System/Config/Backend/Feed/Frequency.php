<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Adminhtml\System\Config\Backend\Feed;

use Magento\Cron\Model\Config\Source\Frequency as CoreFrequency;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Frequency extends Value
{
    private const PATH_CRON_TIME   = 'ff_cron_time';
    private const CRON_STRING_PATH = 'crontab/default/jobs/factfinder_feed_export/schedule/cron_expr';

    /** @var WriterInterface */
    protected $configWriter;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        WriterInterface $configWriter,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->configWriter = $configWriter;
    }

    /**
     * @return Value
     * @throws LocalizedException
     */
    public function afterSave()
    {
        $time             = $this->getFieldsetDataValue(self::PATH_CRON_TIME);
        $frequency        = $this->getValue();
        $frequencyWeekly  = CoreFrequency::CRON_WEEKLY;
        $frequencyMonthly = CoreFrequency::CRON_MONTHLY;

        $cronExprArray  = [
            (int) $time[1],
            (int) $time[0],
            ($frequency == $frequencyMonthly) ? '1' : '*',
            '*',
            ($frequency == $frequencyWeekly) ? '1' : '*',
        ];
        $cronExprString = join(' ', $cronExprArray);

        try {
            $this->configWriter->save(self::CRON_STRING_PATH, $cronExprString);
        } catch (\Exception $e) {
            throw new LocalizedException(__('Unable to save the cron expression.'), $e);
        }

        return $this;
    }
}
