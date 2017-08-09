<?php
namespace Tests\Fei\Service\Audit\Client;

use Codeception\Test\Unit;
use Fei\Service\Audit\Client\Builder\OperatorBuilder;

class OperatorBuilderTest extends Unit
{
    public function testLike()
    {
        /** @var OperatorBuilder $operatorBuilder */
        $operatorBuilder = $this->getMockForAbstractClass(OperatorBuilder::class, [], '', false);
        $operatorBuilder->expects($this->once())->method('build');
        $result = $operatorBuilder->like('fake');

        $this->assertEquals($operatorBuilder, $result);
    }

    public function testbeginsWith()
    {
        /** @var OperatorBuilder $operatorBuilder */
        $operatorBuilder = $this->getMockForAbstractClass(OperatorBuilder::class, [], '', false);
        $operatorBuilder->expects($this->once())->method('build');
        $result = $operatorBuilder->beginsWith('fake');

        $this->assertEquals($operatorBuilder, $result);
    }

    public function testEndsWith()
    {
        /** @var OperatorBuilder $operatorBuilder */
        $operatorBuilder = $this->getMockForAbstractClass(OperatorBuilder::class, [], '', false);
        $operatorBuilder->expects($this->once())->method('build');
        $result = $operatorBuilder->endsWith('fake');

        $this->assertEquals($operatorBuilder, $result);
    }
}