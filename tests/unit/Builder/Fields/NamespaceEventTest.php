<?php
namespace Tests\Fei\Service\Audit\Client;

use Codeception\Test\Unit;
use Fei\Service\Audit\Client\Builder\Fields\NamespaceEvent;
use Fei\Service\Audit\Client\Builder\SearchBuilder;

class NamespaceEventTest extends Unit
{
    public function testBuild()
    {
        $builder = new SearchBuilder();
        $level = new NamespaceEvent($builder);

        $level->build('fake-ns');

        $this->assertEquals([
            'namespace' => 'fake-ns'
        ], $builder->getParams());
    }
}