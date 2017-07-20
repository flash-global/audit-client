<?php
namespace Tests\Fei\Service\Audit\Client;

use Codeception\Test\Unit;
use Fei\Service\Audit\Client\Builder\Fields\Env;
use Fei\Service\Audit\Client\Builder\SearchBuilder;

class EnvTest extends Unit
{
    public function testBuild()
    {
        $builder = new SearchBuilder();
        $env = new Env($builder);

        $env->build('fake-env');

        $this->assertEquals([
            'env' => 'fake-env'
        ], $builder->getParams());
    }
}