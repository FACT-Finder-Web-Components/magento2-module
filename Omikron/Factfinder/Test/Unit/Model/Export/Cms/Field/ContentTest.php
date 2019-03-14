<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Email\Model\Template\Filter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ContentTest extends TestCase
{
    /** @var MockObject|Filter */
    private $filterMock;

    /** @var Content */
    private $contentField;

    /** @var string */
    private $content = '<style>some styles</style> Page Content&nbsp;<script type="text/javascript">javascript magic</script>{{}}';

    public function test_replace_unwanted_expressions()
    {
        $pageMock = $this->createConfiguredMock(PageInterface::class, ['getContent' => $this->content]);
        $filtered = $this->contentField->getValue($pageMock);

        $this->assertFalse(strpos($filtered, 'style'), 'Content should be cleared from \<style\> occurrences');
        $this->assertFalse(strpos($filtered, 'style'), 'Content should be cleared from \<script\> occurrences');
        $this->assertFalse(strpos($filtered, '&nbsp;'), 'Content should be cleared from @nbsp; occurrences');
    }

    protected function setUp()
    {
        $this->filterMock   = $this->createConfiguredMock(Filter::class, ['filter' => $this->content]);
        $this->contentField = new Content($this->filterMock);
    }
}
