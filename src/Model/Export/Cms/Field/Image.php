<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Email\Model\Template\Filter;
use Omikron\Factfinder\Api\Export\FieldInterface;
use Magento\Framework\Model\AbstractModel;

class Image implements FieldInterface
{
    /**
     * phpcs:disable Squiz.WhiteSpace.ScopeClosingBrace.ContentBefore
     * phpcs:disable Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine
     */
    public function __construct(private readonly Filter $filter) {}

    public function getName(): string
    {
        return 'Image';
    }

    /**
     * @param PageInterface $page
     * @return string
     */
    public function getValue(AbstractModel $page): string
    {
        $pattern = '#https?://[^/\s]+/\S+\.(jpe?g|png|gif)#i';
        preg_match($pattern, $this->filter->filter((string) $page->getContent()), $result);
        return $result[0] ?? '';
    }
}
