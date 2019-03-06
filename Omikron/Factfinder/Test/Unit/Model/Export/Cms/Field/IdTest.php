<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use PHPUnit\Framework\TestCase;

class IdTest extends TestCase
{
    /** @var Id */
    private $idField;

    public function test_id_prefix_should_be_added()
    {
        $pageMock = $this->createConfiguredMock(PageInterface::class, ['getId' => 1]);
        $id = $this->idField->getValue($pageMock);
        $this->assertStringStartsWith('P', $id);
        $this->assertStringEndsWith('1', $id);
    }

    protected function setUp()
    {
        $this->idField = new Id();
    }
}
