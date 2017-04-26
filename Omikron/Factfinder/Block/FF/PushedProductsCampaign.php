<?php

namespace Omikron\Factfinder\Block\FF;

use Magento\Framework\View\Element\Template;
use Magento\Catalog\Helper\Image as ImageHelper;

/**
 * Block Class FF PushedProductsCampaign
 * @package Omikron\Factfinder\Block\FF
 */
class PushedProductsCampaign extends Template
{
  /** @var ImageHelper */
  private $_imageHelper;

    /**
     * PushedProductsCampaign constructor.
     *
     * @param Template\Context $context
     * @param array $data
     * @param ImageHelper $imageHelper
     */
    public function __construct(Template\Context $context, $data = [], ImageHelper $imageHelper)
  {
    parent::__construct($context, $data);
    $this->_imageHelper = $imageHelper;
  }
}