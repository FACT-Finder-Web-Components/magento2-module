<?php

namespace Omikron\Factfinder\CustomerData\FF;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Omikron\Factfinder\Helper\Data as DataHelper;
use Omikron\Factfinder\Helper\Tracking;

class Communication implements SectionSourceInterface
{
    /** @var DataHelper */
    private $helper;

    /** @var Tracking */
    private $tracking;

    public function __construct(
        DataHelper $dataHelper,
        Tracking $tracking
    ) {
        $this->helper   = $dataHelper;
        $this->tracking = $tracking;
    }

    /**
     * Provide current session and user ID
     *
     * @return array
     */
    public function getSectionData()
    {
        return [
            'attributes' => [
                'uid' => $this->tracking->getUserId(),
                'sid' => $this->helper->getCorrectSessionId($this->tracking->getSessionId()),
            ],
        ];
    }
}
