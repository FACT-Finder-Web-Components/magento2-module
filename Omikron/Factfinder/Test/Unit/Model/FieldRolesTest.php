<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Magento\Config\Model\ResourceModel\Config as ConfigResource;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Omikron\Factfinder\Api\FieldRolesInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FieldRolesTest extends TestCase
{
    /** @var MockObject|SerializerInterface */
    private $serializerMock;

    /** @var MockObject|ScopeConfigInterface */
    private $scopeConfigMock;

    /** @var MockObject|ConfigResource */
    private $configResourceMock;

    /** @var FieldRolesInterface */
    private $fieldRoles;

    public function test_get_field_role_should_return_correct_array_element()
    {
        $wantedRole       = 'masterArticleNumber';
        $fieldRoles = '{"description":"Description","masterArticleNumber":"MasterProductNumber","price":"Price","productName":"Name","trackingProductNumber":"ProductNumber"}';
        $this->scopeConfigMock->expects($this->once())->method('getValue')->with(FieldRoles::PATH_PRODUCT_FIELD_ROLE)->willReturn($fieldRoles);
        $this->serializerMock->expects($this->once())->method('unserialize')->with($fieldRoles)->willReturn(json_decode($fieldRoles, true));
        $this->assertSame('MasterProductNumber', $this->fieldRoles->getFieldRole($wantedRole));
    }

    public function test_save_get_field_role_should_return_true_if_value_is_serialized_string()
    {
        $valueToSave = '{"description":"Description","masterArticleNumber":"MasterProductNumber"}';
        $this->serializerMock->expects($this->once())->method('unserialize')->with($valueToSave);

        $this->assertTrue($this->fieldRoles->saveFieldRoles($valueToSave, 1));
    }

    public function test_save_get_field_role_should_return_false_if_value_is_not_serialized_string()
    {
        $valueToSave = 'unserializable string';
        $this->serializerMock->expects($this->once())->method('unserialize')->with($valueToSave)->willThrowException(new \InvalidArgumentException());

        $this->assertFalse($this->fieldRoles->saveFieldRoles($valueToSave, 1));
    }

    protected function setUp()
    {
        $this->serializerMock = $this->createMock(SerializerInterface::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->configResourceMock  = $this->createMock(ConfigResource::class);

        $this->fieldRoles = new FieldRoles(
            $this->serializerMock,
            $this->scopeConfigMock,
            $this->configResourceMock
        );
    }
}
