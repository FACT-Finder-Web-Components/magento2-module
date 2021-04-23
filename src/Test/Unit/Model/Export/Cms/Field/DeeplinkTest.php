<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Export\Cms\Field;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers Deeplink
 */
class DeeplinkTest extends TestCase
{
    /** @var MockObject|UrlInterface */
    private $urlBuilderMock;

    /** @var MockObject|StoreManagerInterface */
    private $storeManagerMock;

    /** @var Deeplink */
    private $deeplinkField;

    public function test_url_builder_should_be_called_with_correct_route_parameters()
    {
        $pageMock = $this->getMockBuilder(AbstractModel::class)
            ->disableOriginalConstructor()
            ->setMethods(['getIdentifier'])
            ->getMock();
        $pageMock->method('getIdentifier')->willReturn('test-page');

        $this->urlBuilderMock->expects($this->once())->method('getUrl')->with($this->anything(), ['_direct' => 'test-page'])->willReturn('some address');
        $this->deeplinkField->getValue($pageMock);
    }

    protected function setUp(): void
    {
        $this->urlBuilderMock   = $this->createMock(UrlInterface::class);
        $this->storeManagerMock = $this->createConfiguredMock(StoreManagerInterface::class, [
            'getStore' => $this->createMock(StoreInterface::class),
        ]);

        $this->deeplinkField = new Deeplink($this->urlBuilderMock, $this->storeManagerMock);
    }
}
