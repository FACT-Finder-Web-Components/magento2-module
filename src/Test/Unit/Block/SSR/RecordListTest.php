<?php

namespace Omikron\Factfinder\Block\SSR;

use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\App\Response\Redirect;
use Omikron\Factfinder\Model\FieldRoles;
use Omikron\Factfinder\Model\Ssr\SearchAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Omikron\Factfinder\Block\Ssr\RecordList;
use Magento\Framework\View\Element\Template\Context;

class RecordListTest extends \PHPUnit\Framework\TestCase
{
    /** @var MockObject|Redirect */
    private $redirectMock;

    /** @var RecordList  */
    private $recordList;

    public function test_will_detect_relative_url()
    {
        $isAbsoluteUrlMethod = $this->invokeMethod($this->recordList, 'isAbsoluteUrl', ['/relative-url']);
        $this->assertEquals(false, $isAbsoluteUrlMethod);

    }

    public function test_will_remove_forward_slash()
    {
        $removeForwardSlashMethod = $this->invokeMethod($this->recordList, 'removeForwardSlash', ['/relative-url']);
        $this->assertEquals('relative-url', $removeForwardSlashMethod);
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    protected function setUp(): void
    {
        $this->redirectMock = $this->createMock(RedirectInterface::class);
        $fieldRolesMock = $this->createMock(FieldRoles::class);
        $fieldRolesMock
            ->method('getFieldRole')
            ->willReturnMap(
                [
                    ['price', null, 'Price'],
                    ['deeplink', null, 'Deeplink']
                ]);

        $this->recordList = new RecordList(
            $this->createMock(Context ::class),
            $this->createMock(SearchAdapter::class),
            $this->createMock(SerializerInterface::class),
            $this->createMock(HttpResponse::class),
            $this->redirectMock,
            $fieldRolesMock
        );

    }
}
