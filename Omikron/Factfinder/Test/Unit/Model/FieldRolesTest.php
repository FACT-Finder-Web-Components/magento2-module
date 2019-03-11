<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Omikron\Factfinder\Api\FieldRolesInterface;
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

    public function test_get_field_role_should_return_correct_array_element()
    {
        $wantedRole = 'masterArticleNumber';
        $fieldRoles = '{"description":"Description","masterArticleNumber":"MasterProductNumber","price":"Price","productName":"Name","trackingProductNumber":"ProductNumber"}';
        $this->scopeConfigMock->expects($this->once())->method('getValue')->with('factfinder/general/tracking_product_number_field_role')->willReturn($fieldRoles);
        $this->assertSame('MasterProductNumber', $this->fieldRoles->getFieldRole($wantedRole));
    }

    public function test_save_get_field_role_should_return_true_if_value_is_serialized_string()
    {
        $valueToSave = ['description' => 'Description', 'masterArticleNumber' => 'MasterProductNumber'];
        $this->configResourceMock->expects($this->once())
            ->method('saveConfig')
            ->with('factfinder/general/tracking_product_number_field_role', $this->serializer->serialize($valueToSave));
        $this->assertTrue($this->fieldRoles->saveFieldRoles($valueToSave, 1));
    }

    protected function setUp()
    {
        $this->serializer         = new JsonSerializer();
        $this->scopeConfigMock    = $this->createMock(ScopeConfigInterface::class);
        $this->configResourceMock = $this->createMock(ConfigResource::class);
        $this->fieldRoles         = new FieldRoles($this->serializer, $this->scopeConfigMock, $this->configResourceMock);
    }
}
