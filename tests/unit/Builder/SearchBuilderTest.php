<?php
namespace Tests\Fei\Service\Audit\Client;

use Codeception\Test\Unit;
use Fei\Service\Audit\Client\Builder\Fields\Category;
use Fei\Service\Audit\Client\Builder\Fields\Context;
use Fei\Service\Audit\Client\Builder\Fields\Env;
use Fei\Service\Audit\Client\Builder\Fields\Level;
use Fei\Service\Audit\Client\Builder\Fields\Message;
use Fei\Service\Audit\Client\Builder\Fields\NamespaceEvent;
use Fei\Service\Audit\Client\Builder\Fields\ReportedAt;
use Fei\Service\Audit\Client\Builder\Fields\Server;
use Fei\Service\Audit\Client\Builder\SearchBuilder;
use Fei\Service\Audit\Client\Exception\AuditException;

class SearchBuilderTest extends Unit
{

    public function testNamespaceEvent()
    {
        $builder = new SearchBuilder();

        $this->assertEquals(
            new NamespaceEvent($builder),
            $builder->namespaceEvent()
        );
    }

    public function testLevel()
    {
        $builder = new SearchBuilder();

        $this->assertEquals(
            new Level($builder),
            $builder->level()
        );
    }

    public function testEnv()
    {
        $builder = new SearchBuilder();

        $this->assertEquals(
            new Env($builder),
            $builder->env()
        );
    }

    public function testCategory()
    {
        $builder = new SearchBuilder();

        $this->assertEquals(
            new Category($builder),
            $builder->category()
        );
    }

    public function testMessage()
    {
        $builder = new SearchBuilder();

        $this->assertEquals(
            new Message($builder),
            $builder->message()
        );
    }

    public function testReportedAt()
    {
        $builder = new SearchBuilder();

        $this->assertEquals(
            new ReportedAt($builder),
            $builder->reportedAt()
        );
    }

    public function testServer()
    {
        $builder = new SearchBuilder();

        $this->assertEquals(
            new Server($builder),
            $builder->server()
        );
    }

    public function testContext()
    {
        $builder = new SearchBuilder();

        $this->assertEquals(
            new Context($builder),
            $builder->context()
        );
    }

    public function testContextConditionWhenTypeIsWrong()
    {
        $builder = new SearchBuilder();

        $this->expectException(AuditException::class);
        $this->expectExceptionMessage('Type has to be either "AND" or "OR"!');

        $builder->contextCondition('FAKE');
    }

    public function testContextConditionWhenTypeIsOK()
    {
        $builder = new SearchBuilder();

        $return = $builder->contextCondition('AND');

        $this->assertEquals($builder, $return);
        $this->assertEquals([
            'context_condition' => 'AND'
        ], $return->getParams());
    }

    public function testParamsAccessors()
    {
        $builder = new SearchBuilder();
        $builder->setParams(['params']);

        $this->assertEquals(['params'], $builder->getParams());
        $this->assertAttributeEquals($builder->getParams(), 'params', $builder);
    }

    public function testToCamelCase()
    {
        $builder = new SearchBuilder();

        $this->assertEquals('This-IsA-fake', $builder->toCamelCase('This-Is_a-fake'));
        $this->assertEquals('ThisIsAFake', $builder->toCamelCase('This_Is_a_fake'));
    }

    public function testCall()
    {
        $builder = new SearchBuilder();

        $this->assertEquals(new Level($builder), $builder->__call('level', []));
    }

    public function testCallWhenClassDoesNotExists()
    {
        $builder = new SearchBuilder();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Cannot load fake filter!');

        $builder->fake();
    }
}