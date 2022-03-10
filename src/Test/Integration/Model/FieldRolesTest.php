<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Integration\Model;

use PHPUnit\Framework\TestCase;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Omikron\Factfinder\Model\FieldRoles as FieldRolesModel;

class FieldRolesTest extends TestCase
{
    private ObjectManagerInterface $objectManager;

    public function test_will_store_field_roles_correctly_for_specific_scope()
    {
        $fieldRoles = $this->objectManager->get(FieldRolesModel::class);
        $scopeConfig = $this->objectManager->get(SCopeConfigInterface::class);

        $fieldRoles->saveFieldRoles(['productNumber' => 'ProductNumber'], 0);
        $fieldRoles->saveFieldRoles(['productNumber' => 'ProductIdentifier'], 1);

        $this->assertEqual('ProductNumber', $this->getFieldRole('productNumber', 0));
        $this->assertEqual('ProductIdentifier', $this->getFieldRole('productNumber', 1));
    }

    public function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }
}
