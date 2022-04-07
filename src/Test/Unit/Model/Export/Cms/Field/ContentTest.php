<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Email\Model\Template\Filter;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\Model\AbstractModel;
use PHPUnit\Framework\TestCase;

/**
 * @covers Content
 */
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
        $pageMock = $this->getMockBuilder(AbstractModel::class)
                         ->disableOriginalConstructor()
                         ->setMethods(['getContent'])
                         ->getMock();
        $pageMock->method('getContent')->willReturn($this->content);

        $filtered = $this->contentField->getValue($pageMock);

        $this->assertFalse(strpos($filtered, 'style'), 'Content should be cleared from \<style\> occurrences');
        $this->assertFalse(strpos($filtered, 'style'), 'Content should be cleared from \<script\> occurrences');
        $this->assertFalse(strpos($filtered, '&nbsp;'), 'Content should be cleared from @nbsp; occurrences');
    }

    protected function setUp(): void
    {
        $this->filterMock   = $this->createConfiguredMock(Filter::class, ['filter' => $this->content]);
        $this->contentField = new Content($this->filterMock);
    }
}
