<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Test\Unit\Model\Config;

use Omikron\Factfinder\Api\Config\ParametersSourceInterface;
use PHPUnit\Framework\TestCase;

/**
 * @covers CommunicationParametersProvider
 */
class CommunicationParametersProviderTest extends TestCase
{
    /**
     * @testdox A TypeError should be thrown if source is not implementing ParametersSourceInterface
     */
    public function test_get_parameters_thrown_an_exception_if_source_is_wrong_type()
    {
        $parametersProvider = new CommunicationParametersProvider([
            $this->createMock(ParametersSourceInterface::class),
            $this->createMock(ParametersSourceInterface::class),
            'Not a object',
        ]);

        $this->expectException(\TypeError::class);
        $parametersProvider->getParameters();
    }

    public function test_get_parameters_should_merge_parameters_from_all_providers()
    {
        $parametersProvider = new CommunicationParametersProvider([
            $this->createConfiguredMock(ParametersSourceInterface::class, ['getParameters' => ['param1' => 'value1']]),
            $this->createConfiguredMock(ParametersSourceInterface::class, ['getParameters' => ['param2' => 'value2']]),
            $this->createConfiguredMock(ParametersSourceInterface::class, ['getParameters' => ['param3' => 'value3']]),
        ]);

        $parameters = $parametersProvider->getParameters();
        $this->assertSame(['param1' => 'value1', 'param2' => 'value2', 'param3' => 'value3'], $parameters);
    }
}
