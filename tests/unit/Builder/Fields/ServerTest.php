<?php
namespace Tests\Fei\Service\Audit\Client;

use Codeception\Test\Unit;
use Fei\Service\Audit\Client\Builder\Fields\Server;
use Fei\Service\Audit\Client\Builder\SearchBuilder;

class ServerTest extends Unit
{
    public function testBuild()
    {
        $builder = new SearchBuilder();
        $server = new Server($builder);

        $server->build('fake-server');

        $this->assertEquals([
            'server' => 'fake-server'
        ], $builder->getParams());
    }
}