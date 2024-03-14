<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Integration\Block\SSR;

use GuzzleHttp\Psr7\Response;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\App\Response\Redirect;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\TestCase\AbstractController;
use Omikron\FactFinder\Communication\Client\ClientBuilder;
use Omikron\FactFinder\Communication\Client\ClientInterface;
use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\FieldRoles;
use PHPUnit\Framework\Constraint\StringEndsWith;
use PHPUnit\Framework\MockObject\MockObject;

class RecordListTest extends AbstractController
{
    private ObjectManagerInterface $objectManager;

    /** @var MockObject|ClientInterface */
    private MockObject $clientMock;

    /** @var MockObject|Redirect */
    private MockObject $redirectMock;

    public function test_will_redirect_to_product_page_on_articleNumberSearch()
    {
        $body         = include __DIR__ . '/../../_files/ng_search_response.php';
        $responseMock = new Response(200, [], $body);
        $this->clientMock
            ->expects($this->atLeastOnce())
            ->method('request')
            ->with('GET', $this->stringContains('search'), $this->anything())->willReturn($responseMock);

        $this->redirectMock->expects($this->once())->method('redirect');
        $this->dispatch('/factfinder/result/?query=joust%20duffle');
    }

    public function test_will_not_redirect_to_product_page_if_not_articleNumberSearch()
    {
        $body                      = json_decode(include __DIR__ . '/../../_files/ng_search_response.php');
        $body->articleNumberSearch = false;
        $responseMock              = new Response(200, [], json_encode($body));

        $this->clientMock
            ->expects($this->atLeastOnce())
            ->method('request')
            ->with('GET', $this->stringContains('search'), $this->anything())->willReturn($responseMock);

        $this->redirectMock->expects($this->never())->method('redirect');
        $this->dispatch('/factfinder/result/?query=bags');
    }

    public function test_will_not_lead_to_home_page_when_have_relative_product_url()
    {
        $this->_objectManager->removeSharedInstance(Redirect::class);
        $body                                  = json_decode(include __DIR__ . '/../../_files/ng_search_response.php');
        $body->hits[0]->masterValues->Deeplink = '/joust-duffle-bag.html';
        $responseMock                          = new Response(200, [], json_encode($body));

        $this->clientMock
            ->expects($this->atLeastOnce())
            ->method('request')
            ->with('GET', $this->stringContains('search'), $this->anything())->willReturn($responseMock);

        $this->dispatch('/factfinder/result/?query=joust%20duffle');
        $this->assertRedirect(new StringEndsWith('joust-duffle-bag.html/'));
    }

    protected function setUp(): void
    {
        $this->_objectManager = Bootstrap::getObjectManager();
        $this->clientMock     = $this->createMock(ClientInterface::class);

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
        $fieldRolesMock     = $this->createMock(FieldRoles::class);
        $fieldRolesMock
            ->method('getFieldRole')
            ->willReturnMap(
                [
                    ['price', null, 'Price'],
                    ['deeplink', null, 'Deeplink'],
                    ['imageUrl', null, 'ImageURl']
                ]
            );

        //enable ssr
        $this->_objectManager->get(\Magento\Config\Model\ResourceModel\Config::class)->saveConfig('factfinder/general/use_ssr', 1);
        $this->_objectManager->addSharedInstance($clientBuilderMock, ClientBuilder::class);
        $this->_objectManager->addSharedInstance($communicationConfigMock, CommunicationConfig::class);
        $this->_objectManager->addSharedInstance($this->redirectMock, Redirect::class);
        $this->_objectManager->addSharedInstance($fieldRolesMock, FieldRoles::class);
    }

    protected function tearDown(): void
    {
        $this->_objectManager->removeSharedInstance(ClientBuilder::class);
        $this->_objectManager->removeSharedInstance(CommunicationConfig::class);
        $this->_objectManager->removeSharedInstance(Redirect::class);
        $this->_objectManager->removeSharedInstance(FieldRoles::class);

        parent::tearDown();
    }
}
