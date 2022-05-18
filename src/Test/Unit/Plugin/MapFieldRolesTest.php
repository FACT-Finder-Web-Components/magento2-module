<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Plugin;

use Omikron\Factfinder\Model\Config\CommunicationConfig;
use Omikron\Factfinder\Model\FieldRoles;
use Omikron\Factfinder\Plugin\MapFieldRoles;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MapFieldRolesTest extends TestCase
{
    /** @var MockObject|CommunicationConfig */
    private $configMock;

    /** @var MapFieldRoles */
    private $plugin;

    private $fieldRoles = [
        'brand'         => 'Manufacturer',
        'deeplink'      => 'Deeplink',
        'description'   => 'Description',
        'imageUrl'      => 'ImageUrl',
        'masterId'      => 'Master',
        'price'         => 'Price',
        'productName'   => 'Name',
        'productNumber' => 'ProductNumber',
    ];

    public function test_it_will_map_field_roles_in_ng()
    {
        $this->configMock->method('getVersion')->willReturn('ng');
        $callbackMock = function (array $fieldRoles, int $storeId) {
            $this->assertArrayHasKey('masterArticleNumber', $fieldRoles);
        };
        $this->plugin->aroundSaveFieldRoles($this->createMock(FieldRoles::class), $callbackMock, $this->fieldRoles, 1);
    }

    public function test_it_will_not_map_field_roles_in_pre_ng()
    {
        $this->configMock->method('getVersion')->willReturn('7.3');
        $callbackMock = function (array $fieldRoles, int $storeId) {
            //in fact, this field role does exist in 7.3 but mocked field roles are of NG format
            $this->assertArrayNotHasKey('masterArticleNumber', $fieldRoles);
        };
        $this->plugin->aroundSaveFieldRoles($this->createMock(FieldRoles::class), $callbackMock, $this->fieldRoles, 1);
    }

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(CommunicationConfig::class);
        $this->plugin     = new MapFieldRoles($this->configMock);
    }
}
