<?php
namespace Tests\Fei\Service\Audit\Client;

use Codeception\Test\Unit;
use Fei\Service\Audit\Client\Builder\Fields\Category;
use Fei\Service\Audit\Client\Builder\Fields\Context;
use Fei\Service\Audit\Client\Builder\SearchBuilder;

class ContextTest extends Unit
{
    public function testBuild()
    {
        $builder = new SearchBuilder();
        $context = new Context($builder);

        $context->key('fake-key')->build('fake-context', 'OR');

        $this->assertEquals([
            'context_value' => ['fake-context'],
            'context_operator' => ['OR'],
            'context_key' => ['fake-key']
        ], $builder->getParams());
    }
}