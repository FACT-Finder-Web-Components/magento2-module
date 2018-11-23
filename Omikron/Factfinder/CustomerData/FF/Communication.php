<?php
namespace Omikron\Factfinder\CustomerData\FF;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Omikron\Factfinder\Helper\Data as FactfinderHelper;
use Omikron\Factfinder\Helper\Tracking;

/**
 * Class Communication
 */
class Communication implements SectionSourceInterface
{
    /**
     * @var Tracking
     */
    protected $tracking;

    /**
     * @var FactfinderHelper
     */
    protected $factfinderHelper;

    /**
     * Comunication constructor.
     * @param FactfinderHelper $factfinderHelper
     * @param Tracking $tracking
     */
    public function __construct(
        FactfinderHelper $factfinderHelper,
        Tracking $tracking
    )
    {
        $this->factfinderHelper = $factfinderHelper;
        $this->tracking         = $tracking;
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
                        'uid' => $this->tracking->getUserId(),
                        'sid' => $this->factfinderHelper->getCorrectSessionId($this->tracking->getSessionId())
                    ]
            ];
    }
}
