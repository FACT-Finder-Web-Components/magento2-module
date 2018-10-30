<?php
namespace Omikron\Factfinder\CustomerData\FF;

use Magento\Customer\CustomerData\SectionSourceInterface;
use Omikron\Factfinder\Block\FF\Communication;
use Omikron\Factfinder\Helper\Data as FactfinderHelper;
use Omikron\Factfinder\Helper\Tracking;

/**
 * Class Comumnication
 */
class Comumnication implements SectionSourceInterface
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
     * @var Communication
     */
    protected $communicationBlock;

    /**
     * Comunication constructor.
     * @param FactfinderHelper $factfinderHelper
     * @param Tracking $tracking
     * @param Communication $communicationBlock
     */
    public function __construct(
        FactfinderHelper $factfinderHelper,
        Tracking $tracking,
        Communication $communicationBlock
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
