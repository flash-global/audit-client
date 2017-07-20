<?php
namespace Tests\Fei\Service\Audit\Client;

use Codeception\Test\Unit;
use Fei\Service\Audit\Client\Builder\Fields\ReportedAt;
use Fei\Service\Audit\Client\Builder\SearchBuilder;

class ReportedAtTest extends Unit
{
    public function testBuild()
    {
        $builder = new SearchBuilder();
        $reportedAT = new ReportedAt($builder);

        $reportedAT->build('fake-date', 'OR');

        $this->assertEquals([
            'auditEvent_reportedAt' => 'fake-date',
            'auditEvent_reportedAt_operator' => 'OR',
        ], $builder->getParams());
    }

    public function testFrom()
    {
        $builder = new SearchBuilder();
        $reportedAT = new ReportedAt($builder);

        $reportedAT->from('fake-from');

        $this->assertEquals([
            'auditEvent_reportedAt' => 'fake-from',
        ], $builder->getParams());
    }

    public function testTill()
    {
        $builder = new SearchBuilder();
        $reportedAT = new ReportedAt($builder);

        $reportedAT->till('fake-till');

        $this->assertEquals([
            'auditEvent_reportedAt_till' => 'fake-till',
        ], $builder->getParams());
    }
}