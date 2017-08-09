<?php
namespace Tests\Fei\Service\Audit\Client;

use Codeception\Test\Unit;
use Fei\Service\Audit\Client\Builder\Fields\Message;
use Fei\Service\Audit\Client\Builder\SearchBuilder;

class MessageTest extends Unit
{
    public function testBuild()
    {
        $builder = new SearchBuilder();
        $message = new Message($builder);

        $message->build('fake-message', 'OR');

        $this->assertEquals([
            'auditEvent_message' => 'fake-message',
            'auditEvent_operator' => 'OR',
        ], $builder->getParams());
    }
}