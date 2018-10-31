<?php
namespace Omikron\Factfinder\CustomerData\FF;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Omikron\Factfinder\Block\FF\Communication as CommunicationBlock;
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
     * @var CommunicationBlock
     */
    protected $communicationBlock;

    /**
     * Comunication constructor.
     * @param FactfinderHelper $factfinderHelper
     * @param Tracking $tracking
     * @param CommunicationBlock $communicationBlock
     */
    public function __construct(
        FactfinderHelper $factfinderHelper,
        Tracking $tracking,
        CommunicationBlock $communicationBlock
    )
    {
        $this->factfinderHelper = $factfinderHelper;
        $this->tracking = $tracking;
        $this->communicationBlock = $communicationBlock;
    }

    /**
     * Provide current session and user id
     *
     * @return array
     */
    public function getSectionData()
    {
        return ['component' => $this->communicationBlock->getWebComponent()];
    }
}
