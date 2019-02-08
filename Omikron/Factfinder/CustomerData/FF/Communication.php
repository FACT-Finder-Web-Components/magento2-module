<?php

declare(strict_types = 1);

namespace Omikron\Factfinder\CustomerData\FF;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Omikron\Factfinder\Helper\Data as FactfinderHelper;

class Communication implements SectionSourceInterface
{
    /** @var CustomerSession  */
    protected $customerSession;

    /** @var FactfinderHelper  */
    protected $factfinderHelper;

    public function __construct(
        FactfinderHelper $factfinderHelper,
        CustomerSession $customerSession
    ) {
        $this->factfinderHelper = $factfinderHelper;
        $this->customerSession         = $customerSession;
    }

    /**
     * Provide current session and user id
     *
     * @return array
     */
    public function getSectionData()
    {
        return
            [
                'attributes' =>
                    [
                        'uid' => $this->customerSession->getCustomerId(),
                        'sid' => $this->factfinderHelper->getCorrectSessionId($this->customerSession->getSessionId())
                    ]
            ];
    }
}
