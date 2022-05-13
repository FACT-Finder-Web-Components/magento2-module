<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model\Export\Category\Field;

use Omikron\Factfinder\Model\Export\Category\Field\ParentCategory;
use Omikron\Factfinder\Model\Formatter\CategoryPathFormatter;
use PHPUnit\Framework\TestCase;

/**
 * @covers ParentCategory
 */
class ParentCategoryTest extends TestCase
{
    private $parentCategory;

    public function test_field_name_is_camel_case()
    {
        $this->assertEquals('parentCategory', $this->parentCategory->getName());
    }

    protected function setUp(): void
    {
        $this->parentCategory = new ParentCategory($this->createMock(CategoryPathFormatter::class));
    }
}
