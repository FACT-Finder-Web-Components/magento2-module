<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Integration\Block\SSR;

use GuzzleHttp\Psr7\Response;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\App\Response\Redirect;
use Magento\TestFramework\Helper\Bootstrap;
use Omikron\Factfinder\Block\Ssr\RecordList;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientInterface;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\FieldRoles;
use Omikron\Factfinder\Model\Ssr\SearchAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\App\Response\Http as HttpResponse;

class RecordListTest extends \PHPUnit\Framework\TestCase
{
    /** @var MockObject|ClientInterface */
    private $clientMock;

    /** @var ObjectManagerInterface */
    private $objectManager;

    /** @var MockObject|Redirect */
    private $redirectMock;

    private $recordList;

    public function test_will_redirect_to_product_page_on_articleNumberSearch()
    {
        $body         = include(__DIR__ . '/../../_files/ng_search_response.php');
        $responseMock = new Response(200, [], $body);
        $this->clientMock
            ->expects($this->atLeastOnce())
            ->method('request')
            ->with('GET', $this->stringContains('search'), $this->anything())->willReturn($responseMock);

        $block = $this->objectManager->get(LayoutInterface::class)->createBlock(RecordList::class);
        $this->redirectMock->expects($this->once())->method('redirect');

        $block->toHtml();
    }

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
        $this->objectManager = Bootstrap::getObjectManager();
        $this->clientMock    = $this->createMock(ClientInterface::class);

        $clientBuilderMock = $this->createMock(ClientBuilder::class);
        $clientBuilderMock->method('withVersion')->willReturn($clientBuilderMock);
        $clientBuilderMock->method('withServerUrl')->willReturn($clientBuilderMock);
        $clientBuilderMock->method('withCredentials')->willReturn($clientBuilderMock);
        $clientBuilderMock->method('build')->willReturn($this->clientMock);

        $communicationConfigMock = $this->createConfiguredMock(CommunicationConfig::class, [
            'isChannelEnabled' => true,
            'getVersion'       => 'ng'
        ]);

        $this->redirectMock = $this->createMock(RedirectInterface::class);

        $fieldRolesMock = $this->createMock(FieldRoles::class);
        $fieldRolesMock
            ->method('getFieldRole')
            ->willReturnMap(
                [
                    ['price', null, 'Price'],
                    ['deeplink', null, 'Deeplink']
                ]);

        $this->objectManager->addSharedInstance($clientBuilderMock, ClientBuilder::class);
        $this->objectManager->addSharedInstance($communicationConfigMock, CommunicationConfig::class);
        $this->objectManager->addSharedInstance($this->redirectMock, Redirect::class);
        $this->objectManager->addSharedInstance($fieldRolesMock, FieldRoles::class);

        $this->recordList = new RecordList($this->createMock(Template\Context::class), $this->createMock(SearchAdapter::class), $this->createMock(SerializerInterface::class), $this->createMock(HttpResponse::class), $this->redirectMock, $fieldRolesMock);
    }

    protected function tearDown(): void
    {
        $this->objectManager->removeSharedInstance(ClientBuilder::class);
        parent::tearDown();
    }
}
