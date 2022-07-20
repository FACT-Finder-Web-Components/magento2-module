<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\Product\Edit\Button;

use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;

class ExportPreview extends Generic
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData(): array
    {
        $url = $this->getUrl(
            'factfinder/export/preview',
            [
                'entityId' => (int) $this->context->getRequestParam('id', 0),
            ]
        );

        return [
            'label' => __('Export Preview'),
            'class' => 'action-secondary',
            'sort_order' => 20,
            'on_click' => sprintf("window.open('%s', '_blank');", $url),
        ];
    }
}
