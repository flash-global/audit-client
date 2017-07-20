<?php
namespace Tests\Fei\Service\Audit\Client;

use Codeception\Test\Unit;
use Fei\Service\Audit\Client\Builder\Fields\Level;
use Fei\Service\Audit\Client\Builder\SearchBuilder;

class LevelTest extends Unit
{
    public function testBuild()
    {
        $builder = new SearchBuilder();
        $level = new Level($builder);

        $level->build('fake-level');

        $this->assertEquals([
            'level' => 'fake-level'
        ], $builder->getParams());
    }
}