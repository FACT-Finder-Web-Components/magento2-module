<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model\Http;

use PHPUnit\Framework\TestCase;

class ParameterUtilsTest extends TestCase
{
    /** @var ParameterUtils */
    private $paramUtils;

    public function test_fixed_get_params_should_replace_underscore_with_plus_for_any_tree_type_parameter()
    {
        $lvl2CategoryFilterName = 'filterCategoryPathROOT/First_Category';

        $fixedParams = $this->paramUtils->fixedGetParams([
            'filterCategoryPathROOT' => 'First Category',
            $lvl2CategoryFilterName  => 'Second Category',
        ]);

        $this->assertArrayHasKey(
            str_replace('_', ' ', $lvl2CategoryFilterName),
            $fixedParams,
            'filterCategory parameters should have "_" changed to " "'
        );
    }

    protected function setUp()
    {
        $this->paramUtils = new ParameterUtils();
    }
}
