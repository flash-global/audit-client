<?php
/**
 * AuditAwareTraitTest.php
 *
 * @date        9/08/17
 * @file        AuditAwareTraitTest.php
 */

namespace Tests\Fei\Service\Audit\Client;

use Codeception\Test\Unit;
use Fei\Service\Audit\Client\Audit;
use Fei\Service\Audit\Client\Utils\AuditAwareTrait;

/**
 * AuditAwareTraitTest
 */
class AuditAwareTraitTest extends Unit
{
    public function testGetterSetter() {
        $mock = $this->getMockBuilder(Audit::class)->getMock();

        $instance = $this->getMockForTrait(AuditAwareTrait::class);

        $instance->setAuditClient($mock);
        $this->assertInstanceOf(Audit::class, $instance->getAuditClient($mock));
    }
}