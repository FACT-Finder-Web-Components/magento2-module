<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Http;

use Magento\Framework\App\RequestInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ParameterUtilsTest extends TestCase
{
    /** @var ParameterUtils */
    private $paramUtils;

    private $requestMock;

    public function test_fixed_get_params_should_replace_underscore_with_plus_for_any_tree_type_parameter()
    {
        $lvl2CategoryFilterName = 'filterCategoryPathROOT/First_Category';
        $params = [
            'filterCategoryPathROOT' => 'First Category',
            $lvl2CategoryFilterName => 'Second Category',
        ];
        $this->requestMock->expects($this->once())->method('getParams')->willReturn($params);
        $fixedParams = $this->paramUtils->fixedGetParams($this->requestMock);
        $this->assertArrayHasKey(
            str_replace('_', '+', $lvl2CategoryFilterName),
            $fixedParams,
            'filterCategory parameters should have "_" changed to "+"'
        );
    }

    protected function setUp()
    {
        $this->paramUtils = new ParameterUtils();
        $this->requestMock =  $this->createMock(RequestInterface::class);
    }
}
