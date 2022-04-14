<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model\Formatter;

use PHPUnit\Framework\TestCase;

/**
 * @covers NumberFormatter
 */
class NumberFormatterTest extends TestCase
{
    /** @var NumberFormatter */
    private $formatter;

    public function test_it_formats_the_number_with_right_precision()
    {
        $this->assertSame('42.21', $this->formatter->format(42.2051));
        $this->assertSame('42', $this->formatter->format(42.2051, 0));
        $this->assertSame('42.2', $this->formatter->format(42.2051, 1));
        $this->assertSame('42.21', $this->formatter->format(42.2051, 2));
        $this->assertSame('42.205', $this->formatter->format(42.2051, 3));
    }

    protected function setUp(): void
    {
        $this->formatter = new NumberFormatter();
    }
}
