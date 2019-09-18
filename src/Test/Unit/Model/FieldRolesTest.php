<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Omikron\Factfinder\Api\FieldRolesInterface;
use Omikron\Factfinder\Model\Export\Catalog\ProductType\SimpleDataProvider;
use Omikron\Factfinder\Model\Export\Catalog\ProductType\SimpleDataProviderFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FieldRolesTest extends TestCase
{
    /** @var MockObject|ScopeConfigInterface */
    private $scopeConfigMock;

    /** @var MockObject|ConfigResource */
    private $configResourceMock;

    /** @var FieldRolesInterface */
    private $fieldRoles;

    /** @var JsonSerializer */
    private $serializer;

    /** @var SimpleDataProvider */
    private $dataProvider;

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
            ->with('factfinder/general/tracking_product_number_field_role', $this->serializer->serialize($valueToSave));
        $this->assertTrue($this->fieldRoles->saveFieldRoles($valueToSave, 1));
    }

    public function test_field_role_to_attribute_returns_correct_array_value()
    {
        $productMock = $this->createConfiguredMock(ProductInterface::class, ['getSku' => 'sku-1']);
        $this->scopeConfigMock->method('getValue')->with('factfinder/general/tracking_product_number_field_role')->willReturn($this->roles);
        $this->dataProvider->expects($this->once())->method('toArray')->willReturn([
            'ProductNumber' => 'sku-1',
            'Master'        => 'sku-1',
            'Name'          => 'product name',
            'Description'   => 'product description',
            'Short'         => 'product short description',
            'ProductURL'    => 'http://magneto2/product-link.html',
            'Price'         => '9.99',
            'Brand'         => 'Product brand',
            'Availability'  => 1,
            'MagentoId'     => 11,
        ]);
        $brandAttributeValue  = $this->fieldRoles->fieldRoleToAttribute($productMock, 'brand');
        $masterAttributeValue = $this->fieldRoles->fieldRoleToAttribute($productMock, 'masterArticleNumber');
        $this->assertSame('Product brand', $brandAttributeValue);
        $this->assertSame('sku-1', $masterAttributeValue);
    }

    protected function setUp()
    {

        $this->serializer         = new JsonSerializer();
        $this->scopeConfigMock    = $this->createMock(ScopeConfigInterface::class);
        $this->configResourceMock = $this->createMock(ConfigResource::class);
        $this->dataProvider       = $this->createMock(SimpleDataProvider::class);

        $dataProviderFactory = $this->getMockBuilder(SimpleDataProviderFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])
            ->getMock();
        $dataProviderFactory->method('create')->willReturn($this->dataProvider);

        $this->fieldRoles = new FieldRoles(
            $this->serializer,
            $this->scopeConfigMock,
            $this->configResourceMock,
            $dataProviderFactory
        );
    }
}
