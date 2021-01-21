<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Filter;

use Omikron\Factfinder\Api\Filter\FilterInterface;
use PHPUnit\Framework\TestCase;

class ExtendedTextFilterTest extends TestCase
{
    /** @var TextFilter */
    private $filter;

    public function test_it_is_a_filter()
    {
        $this->assertInstanceOf(FilterInterface::class, $this->filter);
    }

    public function test_it_removes_the_forbidden_characters()
    {
        $this->assertSame($this->filter->filterValue('Remove#all|forbidden=chars'), 'Remove all forbidden chars');
    }

    protected function setUp(): void
    {
        $this->filter = new ExtendedTextFilter();
    }
}
