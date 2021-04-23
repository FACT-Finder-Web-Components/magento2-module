<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Ssr\Template;

use Omikron\Factfinder\Model\FieldRoles;
use PHPUnit\Framework\TestCase;

/**
 * @covers FilterTest
 */
class FilterTest extends TestCase
{
    /** @var Filter */
    private $filter;

    public function test_anchor_links_are_replaced()
    {
        $from = '<a class="product-image-container" data-anchor="{{record.ProductURL}}">';
        $to   = '<a class="product-image-container" href="{{record.ProductURL}}" data-anchor="{{record.ProductURL}}">';
        $this->assertSame($this->filter->filterValue($from), $to);
    }

    public function test_data_targets_are_replaced()
    {
        $from = '<a class="product-image-container" data-redirect-target="_blank">';
        $to   = '<a class="product-image-container" target="_blank" data-redirect-target="_blank">';
        $this->assertSame($this->filter->filterValue($from), $to);
    }

    public function test_image_src_are_replaced()
    {
        $from = '<img class="product-image-photo" data-image="{{record.ImageUrl}}" alt="{{record.Name}}" />';
        $to   = '<img class="product-image-photo" src="{{record.ImageUrl}}" data-image="{{record.ImageUrl}}" alt="{{record.Name}}" />';
        $this->assertSame($this->filter->filterValue($from), $to);

        $from = '<img class="product-image-photo" data-image alt="{{record.Name}}" />';
        $to   = '<img class="product-image-photo" src="{{record.CustomUrl}}" data-image alt="{{record.Name}}" />';
        $this->assertSame($this->filter->filterValue($from), $to);
    }

    protected function setUp(): void
    {
        $fieldRoles = $this->createMock(FieldRoles::class);
        $fieldRoles->method('getFieldRole')->with('imageUrl', $this->anything())->willReturn('CustomUrl');
        $this->filter = new Filter($fieldRoles);
    }
}
