<?php

namespace Tests\Fei\Service\Audit\Client;

use Codeception\Test\Unit;
use Fei\ApiClient\Transport\SyncTransportInterface;
use Fei\Service\Audit\Entity\AuditEvent;
use Fei\Service\Audit\Client\Audit;

class AuditEventTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var Audit */
    protected $audit;

    /** @var \Faker\Generator */
    protected $faker;

    protected function _before()
    {
        $this->faker = \Faker\Factory::create('fr_FR');
    }

    public function testAuditCanCommit()
    {
        $audit = new Audit();

        $transport = $this->createMock(SyncTransportInterface::class);
        $transport->expects($this->never())->method('sendMany');
        $audit->setTransport($transport);

        $audit->begin();
        $audit->notify('test');
        $audit->commit();
    }

    public function testAuditCanDelay()
    {
        $audit = new Audit();

        $audit->begin();
        $this->assertAttributeEquals(true, 'isDelayed', $audit);
    }

    public function testAuditCanNotify()
    {
        $audit = new Audit();
        $audit->setBaseUrl('http://azeaze.fr/');

        $auditEvent = new AuditEvent();
        $auditEvent->setMessage($this->faker->sentence);
        $auditEvent->setLevel(AuditEvent::LVL_ERROR);

        $transport = $this->createMock(SyncTransportInterface::class);
        $transport->expects($this->once())->method('send');
        $audit->setTransport($transport);

        $audit->notify($auditEvent);
    }

    public function testAutoCommit()
    {
        $audit = (new Audit())->enableAutoCommit();
        $audit->setBaseUrl('http://azeaze.fr/');

        $auditEvent = new AuditEvent();
        $auditEvent->setMessage($this->faker->sentence);
        $auditEvent->setLevel(AuditEvent::LVL_ERROR);

        $transport = $this->createMock(SyncTransportInterface::class);
        $transport->expects($this->never())->method('send');
        $transport->expects($this->never())->method('sendMany');
        $audit->setTransport($transport);

        $audit->notify($auditEvent);
    }

    public function testBacktrace()
    {
        $audit = new Audit();
        $audit->setBaseUrl('http://azeaze.fr/');

        $auditEvent = new AuditEvent();
        $auditEvent->setMessage($this->faker->sentence);
        $auditEvent->setLevel(AuditEvent::LVL_ERROR);

        $audit->setTransport($this->createMock(SyncTransportInterface::class));

        $notify = function () use ($audit, $auditEvent) {
            $audit->notify($auditEvent);
        };

        $notify();

        $this->assertEquals(19, count($auditEvent->getBackTrace()));
        $this->assertEquals(
            'Tests\Fei\Service\Audit\Client\AuditEventTest->Tests\Fei\Service\Audit\Client\{closure}',
            $auditEvent->getBackTrace()[0]['method']
        );
        $this->assertEquals(
            'Instance of Tests\Fei\Service\Audit\Client\AuditEventTest',
            $auditEvent->getBackTrace()[2]['args'][0]
        );
    }

    public function testWriteToExceptionLogFile()
    {
        $transport = $this->createMock(SyncTransportInterface::class);
        $transport->expects($this->exactly(2))->method('send')->willThrowException(
            new \Exception('this is a message')
        );

        $audit = new Audit([Audit::OPTION_BASEURL => 'http://url']);
        $audit->setTransport($transport);
        $audit->setOption(Audit::OPTION_LOGFILE, __DIR__ . '/test.log');

        $auditEvent = new AuditEvent();
        $auditEvent->setMessage($this->faker->sentence);
        $auditEvent->setLevel(AuditEvent::LVL_ERROR);

        $audit->notify($auditEvent);

        $this->assertTrue(file_exists($audit->getOption(Audit::OPTION_LOGFILE)));

        $this->assertRegExp(
            '/^\[(.*)\] this is a message$/',
            file_get_contents($audit->getOption(Audit::OPTION_LOGFILE))
        );

        $audit->notify($auditEvent);

        $this->assertCount(
            2,
            explode("\n", trim(file_get_contents($audit->getOption(Audit::OPTION_LOGFILE))))
        );

        unlink($audit->getOption(Audit::OPTION_LOGFILE));
    }
}
