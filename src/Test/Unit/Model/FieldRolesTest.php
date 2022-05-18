<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Store\Model\ScopeInterface as Scope;
use Omikron\Factfinder\Model\Export\Catalog\ProductType\SimpleDataProvider;
use Omikron\Factfinder\Model\Export\Catalog\ProductType\SimpleDataProviderFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers FieldRoles
 */
class FieldRolesTest extends TestCase
{
    /** @var FieldRoles */
    private FieldRoles $fieldRoles;

    /** @var JsonSerializer */
    private $serializer;

    /** @var MockObject|ScopeConfigInterface */
    private MockObject $scopeConfigMock;

    /** @var MockObject|ConfigResource */
    private MockObject $configResourceMock;

    /** @var MockObject|SimpleDataProvider */
    private MockObject $dataProviderMock;

    /** @var string */
    private $roles = '{"description":"Description","masterArticleNumber":"Master","price":"Price","productName":"Name","trackingProductNumber":"ProductNumber","brand":"Brand"}';

    public function test_get_field_role_should_return_correct_array_element()
    {
        $wantedRole = 'masterArticleNumber';
        $this->scopeConfigMock->expects($this->once())->method('getValue')->with('factfinder/general/tracking_product_number_field_role')->willReturn($this->roles);
        $this->assertSame('Master', $this->fieldRoles->getFieldRole($wantedRole));
    }

    public function test_save_get_field_role_should_return_true_if_value_is_serialized_string()
    {
        $valueToSave = ['description' => 'Description', 'masterArticleNumber' => 'Master'];
        $this->configResourceMock->expects($this->once())
            ->method('saveConfig')
            ->with('factfinder/general/tracking_product_number_field_role', $this->serializer->serialize($valueToSave), Scope::SCOPE_STORES);
        $this->assertTrue($this->fieldRoles->saveFieldRoles($valueToSave, 1));
    }

    protected function setUp(): void
    {
        $this->serializer         = new JsonSerializer();
        $this->scopeConfigMock    = $this->createMock(ScopeConfigInterface::class);
        $this->configResourceMock = $this->createMock(ConfigResource::class);
        $this->dataProviderMock   = $this->createMock(SimpleDataProvider::class);

        $dataProviderFactory = $this->getMockBuilder(SimpleDataProviderFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $dataProviderFactory->method('create')->willReturn($this->dataProviderMock);

        $this->fieldRoles = new FieldRoles(
            $this->serializer,
            $this->scopeConfigMock,
            $this->configResourceMock,
            $dataProviderFactory
        );
    }
}
